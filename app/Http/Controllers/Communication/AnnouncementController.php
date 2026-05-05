<?php

namespace App\Http\Controllers\Communication;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementPublishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(15);
        return view('communication.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:general,academic,financial,event',
            'audience' => 'required|in:all,students,parents,staff',
            'priority' => 'in:low,normal,high,urgent',
            'send_sms' => 'boolean',
            'send_email' => 'boolean',
        ]);

        $announcement = Announcement::create([
            ...$validated,
            'school_id' => auth()->user()->school_id,
            'created_by' => auth()->id(),
            'published_at' => now(),
            'send_sms' => $request->boolean('send_sms'),
            'send_email' => $request->boolean('send_email'),
        ]);

        try {
            $this->dispatchAnnouncement($announcement);
        } catch (\Throwable $exception) {
            Log::error('Announcement dispatch failed after publish; preserving announcement record.', [
                'announcement_id' => $announcement->id,
                'school_id' => $announcement->school_id,
                'error' => $exception->getMessage(),
            ]);

            return back()->with('success', 'Announcement published. Some deliveries could not be completed.');
        }

        return back()->with('success', 'Announcement published.');
    }

    public function destroy(Announcement $announcement)
    {
        $currentSchoolId = (int) (auth()->user()->school_id ?? 0);
        if ((int) $announcement->school_id !== $currentSchoolId) {
            abort(403, 'Unauthorized announcement action.');
        }

        $announcement->delete();

        return back()->with('success', 'Announcement removed.');
    }

    private function dispatchAnnouncement(Announcement $announcement): void
    {
        $school = auth()->user()?->school;
        $emailNotificationsEnabled = (bool) data_get($school?->settings, 'email_notifications_enabled', false);

        $baseQuery = User::query()
            ->where('school_id', (int) $announcement->school_id)
            ->where('is_active', true);

        $recipients = match ((string) $announcement->audience) {
            'students' => (clone $baseQuery)->where('role', UserRole::STUDENT->value),
            'parents' => (clone $baseQuery)->where('role', UserRole::PARENT->value),
            'staff' => (clone $baseQuery)->whereIn('role', [
                UserRole::STAFF->value,
                UserRole::TEACHER->value,
                UserRole::PRINCIPAL->value,
                UserRole::VICE_PRINCIPAL->value,
                UserRole::ACCOUNTANT->value,
                UserRole::LIBRARIAN->value,
                UserRole::DRIVER->value,
                UserRole::NURSE->value,
            ]),
            default => (clone $baseQuery)->whereIn('role', [
                UserRole::STUDENT->value,
                UserRole::PARENT->value,
                UserRole::STAFF->value,
                UserRole::TEACHER->value,
                UserRole::PRINCIPAL->value,
                UserRole::VICE_PRINCIPAL->value,
                UserRole::ACCOUNTANT->value,
                UserRole::LIBRARIAN->value,
                UserRole::DRIVER->value,
                UserRole::NURSE->value,
            ]),
        };

        $users = $recipients->get();
        if ($users->isEmpty()) {
            Log::info('Announcement dispatch skipped: no matching recipients.', [
                'announcement_id' => $announcement->id,
                'audience' => $announcement->audience,
                'school_id' => $announcement->school_id,
            ]);
            return;
        }

        $sendEmail = (bool) $announcement->send_email && $emailNotificationsEnabled;
        if ($sendEmail) {
            try {
                $this->configureSmtpForSchool($school);
            } catch (\Throwable $exception) {
                Log::warning('Announcement SMTP configuration could not be applied; falling back to default mailer.', [
                    'announcement_id' => $announcement->id,
                    'school_id' => $school?->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
        $notification = new AnnouncementPublishedNotification($announcement, $sendEmail);
        $databaseOnlyNotification = new AnnouncementPublishedNotification($announcement, false);

        foreach ($users as $user) {
            if (!$sendEmail) {
                $user->notify($databaseOnlyNotification);
                continue;
            }

            // Skip obviously non-routable domains (e.g. local/test placeholders).
            if (!$this->canAttemptEmailDelivery((string) $user->email)) {
                $user->notify($databaseOnlyNotification);
                continue;
            }

            try {
                $user->notify($notification);
            } catch (\Throwable $exception) {
                Log::warning('Announcement mail delivery failed for recipient; keeping database notification delivery.', [
                    'announcement_id' => $announcement->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ((bool) $announcement->send_sms) {
            Log::info('Announcement SMS requested but SMS provider integration is not configured in this flow.', [
                'announcement_id' => $announcement->id,
                'audience' => $announcement->audience,
            ]);
        }
    }

    private function configureSmtpForSchool($school): void
    {
        $smtp = (array) data_get($school?->settings, 'smtp', []);
        if (!($smtp['enabled'] ?? false)) {
            return;
        }

        $host = trim((string) ($smtp['host'] ?? ''));
        $port = (int) ($smtp['port'] ?? 0);
        $fromAddress = trim((string) ($smtp['from_address'] ?? ''));
        $encryption = trim((string) ($smtp['encryption'] ?? 'tls'));

        if ($host === '' || $port < 1 || $fromAddress === '') {
            throw new \RuntimeException('SMTP host, port, or from address is missing.');
        }

        $normalizedEncryption = $encryption === 'none' ? null : $encryption;
        $smtpPassword = trim((string) ($smtp['password'] ?? ''));
        if ($smtpPassword !== '') {
            try {
                $smtpPassword = Crypt::decryptString($smtpPassword);
            } catch (\Throwable $exception) {
                // Backward compatibility for plaintext passwords.
            }
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $host,
            'port' => $port,
            'encryption' => $normalizedEncryption,
            'username' => trim((string) ($smtp['username'] ?? '')) ?: null,
            'password' => $smtpPassword ?: null,
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ]);
        Config::set('mail.from.address', $fromAddress);
        Config::set('mail.from.name', trim((string) ($smtp['from_name'] ?? '')) ?: ($school?->name ?? config('app.name', 'School')));
    }

    private function canAttemptEmailDelivery(string $email): bool
    {
        $email = trim(strtolower($email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = Str::after($email, '@');
        if ($domain === '' || !str_contains($domain, '.')) {
            return false;
        }

        foreach (['.test', '.local', '.localhost', '.invalid', '.example'] as $suffix) {
            if (str_ends_with($domain, $suffix)) {
                return false;
            }
        }

        return true;
    }
}
