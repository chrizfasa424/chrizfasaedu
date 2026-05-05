<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class AnnouncementPublishedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Announcement $announcement,
        private readonly bool $sendEmail = false
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->sendEmail && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $targetRoute = $this->targetNotificationsRoute($notifiable);
        $school = $this->announcement->school;
        $recipientName = trim((string) ($notifiable->first_name ?? 'there'));

        return (new MailMessage)
            ->subject('New School Announcement: ' . (string) $this->announcement->title)
            ->view('emails.announcements.published', [
                'announcement' => $this->announcement,
                'school' => $school,
                'schoolName' => $school?->name ?? config('app.name', 'School'),
                'recipientName' => $recipientName,
                'targetUrl' => url($targetRoute),
                'priorityLabel' => ucfirst((string) ($this->announcement->priority ?? 'normal')),
                'typeLabel' => ucfirst((string) ($this->announcement->type ?? 'general')),
                'previewText' => 'A new announcement has been published for you.',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $targetRoute = $this->targetNotificationsRoute($notifiable);

        return [
            'announcement_id' => (int) $this->announcement->id,
            'title' => (string) $this->announcement->title,
            'message' => (string) Str::limit(strip_tags((string) $this->announcement->body), 180),
            'priority' => (string) ($this->announcement->priority ?? 'normal'),
            'type' => (string) ($this->announcement->type ?? 'general'),
            'route' => $targetRoute,
        ];
    }

    private function targetNotificationsRoute(object $notifiable): string
    {
        if (method_exists($notifiable, 'isStudent') && $notifiable->isStudent()) {
            return '/my/notifications';
        }

        if (method_exists($notifiable, 'isParent') && $notifiable->isParent()) {
            return '/my/notifications';
        }

        return '/notifications';
    }
}
