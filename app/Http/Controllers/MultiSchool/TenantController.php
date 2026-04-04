<?php

namespace App\Http\Controllers\MultiSchool;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use App\Support\DomainHelper;
use App\Support\SchoolContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index()
    {
        if (SchoolContext::isSingleSchoolMode()) {
            return redirect()->route('dashboard')->with('success', 'Single-brand mode is enabled. The multi-school dashboard is not used in this installation.');
        }

        $this->ensureSuperAdmin();

        $schools = School::withCount(['students', 'staff'])->latest()->paginate(20);
        $summary = [
            'totalSchools' => School::count(),
            'activeSchools' => School::where('is_active', true)->count(),
            'schoolsOnPage' => $schools->count(),
        ];

        return view('multi-school.index', compact('schools', 'summary'));
    }

    public function onboard(Request $request)
    {
        if (SchoolContext::isSingleSchoolMode()) {
            return redirect()->route('dashboard')->with('error', 'Single-brand mode is enabled. New school onboarding is disabled.');
        }

        $this->ensureSuperAdmin();

        $request->merge([
            'domain' => DomainHelper::normalize($request->input('domain')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:schools,email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'domain' => ['nullable', 'string', 'max:190', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', Rule::unique('schools', 'domain')],
            'school_type' => 'required|in:primary,secondary,combined',
            'plan' => 'required|in:basic,standard,premium,enterprise',
            'admin_name' => 'required|string',
            'admin_email' => 'required|email|unique:users,email',
        ]);

        $school = School::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'code' => strtoupper(Str::random(6)),
            'email' => $validated['email'],
            'domain' => $validated['domain'] ?? null,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'school_type' => $validated['school_type'],
            'subscription_plan' => $validated['plan'],
            'subscription_expires_at' => now()->addYear(),
        ]);

        $nameParts = explode(' ', $validated['admin_name'], 2);
        User::create([
            'school_id' => $school->id,
            'first_name' => $nameParts[0],
            'last_name' => $nameParts[1] ?? $nameParts[0],
            'email' => $validated['admin_email'],
            'password' => Hash::make('changeme123'),
            'role' => 'school_admin',
        ]);

        Subscription::create([
            'school_id' => $school->id,
            'plan_name' => ucfirst($validated['plan']),
            'plan_code' => $validated['plan'],
            'amount' => $this->planPrice($validated['plan']),
            'billing_cycle' => 'yearly',
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
            'is_active' => true,
        ]);

        return redirect()->route('multi-school.index')->with('success', "School {$school->name} onboarded.");
    }

    public function domains(Request $request)
    {
        if (SchoolContext::isSingleSchoolMode()) {
            return redirect()->route('settings.index')->with('success', 'Single-brand mode is enabled. Use Settings to manage the main school profile and domain.');
        }

        $this->ensureSuperAdmin();

        $search = trim((string) $request->query('search', ''));
        $currentHost = DomainHelper::normalize($request->getHost());
        $currentHostIp = $currentHost ? @gethostbyname($currentHost) : null;

        $schools = School::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', '%' . $search . '%')
                        ->orWhere('domain', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'totalSchools' => School::count(),
            'schoolsWithDomains' => School::query()->whereNotNull('domain')->where('domain', '!=', '')->count(),
            'schoolsWithoutDomains' => School::query()->where(function ($query) {
                $query->whereNull('domain')->orWhere('domain', '');
            })->count(),
        ];

        $dnsGuide = [
            'appHost' => $currentHost,
            'appIp' => ($currentHostIp && $currentHostIp !== $currentHost) ? $currentHostIp : null,
        ];

        $domainStatuses = $schools->getCollection()
            ->mapWithKeys(function (School $school) use ($currentHost, $currentHostIp) {
                return [$school->id => $this->resolveDomainStatus($school->domain, $currentHost, $currentHostIp)];
            })
            ->all();

        return view('multi-school.domains', compact('schools', 'summary', 'search', 'domainStatuses', 'dnsGuide'));
    }

    public function updateDomain(Request $request, School $school)
    {
        if (SchoolContext::isSingleSchoolMode()) {
            return redirect()->route('settings.index')->with('error', 'Single-brand mode is enabled. Use Settings to manage the main school domain.');
        }

        $this->ensureSuperAdmin();

        $oldDomain = $school->domain;
        $request->merge([
            'domain' => DomainHelper::normalize($request->input('domain')),
        ]);

        $validated = $request->validate([
            'domain' => ['nullable', 'string', 'max:190', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i', Rule::unique('schools', 'domain')->ignore($school->id)],
        ]);

        $school->update([
            'domain' => $validated['domain'] ?? null,
        ]);

        $this->forgetDomainStatusCache($oldDomain, $request->getHost());
        $this->forgetDomainStatusCache($school->domain, $request->getHost());

        $message = $school->domain
            ? "Domain saved for {$school->name}."
            : "Domain removed for {$school->name}.";

        return back()
            ->with('success', $message)
            ->with('updated_school_id', $school->id);
    }

    public function clearDomainCache(Request $request)
    {
        if (SchoolContext::isSingleSchoolMode()) {
            return redirect()->route('settings.index')->with('success', 'Single-brand mode is enabled. Domain cache is not needed for multi-school routing.');
        }

        $this->ensureSuperAdmin();

        $host = $request->getHost();

        School::query()
            ->pluck('domain')
            ->each(function ($domain) use ($host) {
                $this->forgetDomainStatusCache($domain, $host);
            });

        return back()->with('success', 'Domain status cache cleared. Fresh DNS checks will run on the next page load.');
    }

    private function planPrice(string $plan): float
    {
        return match($plan) {
            'basic' => 50000,
            'standard' => 150000,
            'premium' => 350000,
            'enterprise' => 750000,
            default => 50000,
        };
    }

    private function resolveDomainStatus(?string $domain, ?string $currentHost, ?string $currentHostIp): array
    {
        $normalizedDomain = DomainHelper::normalize($domain);

        if (!$normalizedDomain) {
            return [
                'label' => 'Not Configured',
                'tone' => 'amber',
                'detail' => 'No custom domain has been saved yet.',
                'public_url' => null,
            ];
        }

        $network = Cache::remember('school-domain-network:' . md5($normalizedDomain), now()->addMinutes(10), function () use ($normalizedDomain) {
            $resolvedIp = @gethostbyname($normalizedDomain);

            if (!$resolvedIp || $resolvedIp === $normalizedDomain) {
                return [
                    'resolved' => false,
                    'ip' => null,
                    'https' => false,
                ];
            }

            return [
                'resolved' => true,
                'ip' => $resolvedIp,
                'https' => $this->checkHttpsAvailability($normalizedDomain),
            ];
        });

        if ($currentHost && $normalizedDomain === $currentHost) {
            return [
                'label' => 'Live on Current Host',
                'tone' => request()->isSecure() ? 'emerald' : 'sky',
                'detail' => request()->isSecure()
                    ? 'This domain matches the host currently serving the app over HTTPS.'
                    : 'This domain matches the host currently serving the app, but the current request is not using HTTPS.',
                'public_url' => (request()->isSecure() ? 'https://' : 'http://') . $normalizedDomain,
            ];
        }

        if (!($network['resolved'] ?? false)) {
            return [
                'label' => 'Pending DNS',
                'tone' => 'amber',
                'detail' => 'No active DNS resolution was found for this domain yet.',
                'public_url' => 'http://' . $normalizedDomain,
            ];
        }

        if ($currentHostIp && ($network['ip'] ?? null) === $currentHostIp) {
            if ($network['https'] ?? false) {
                return [
                    'label' => 'Live + HTTPS',
                    'tone' => 'emerald',
                    'detail' => 'The domain resolves to this app host and responds on HTTPS.',
                    'public_url' => 'https://' . $normalizedDomain,
                ];
            }

            return [
                'label' => 'Live / HTTP Only',
                'tone' => 'sky',
                'detail' => 'The domain resolves to this app host, but HTTPS was not detected.',
                'public_url' => 'http://' . $normalizedDomain,
            ];
        }

        if ($network['https'] ?? false) {
            return [
                'label' => 'Configured + HTTPS',
                'tone' => 'sky',
                'detail' => 'DNS resolves and HTTPS is available, but it is not confirmed on this app host.',
                'public_url' => 'https://' . $normalizedDomain,
            ];
        }

        return [
            'label' => 'Configured / No HTTPS',
            'tone' => 'amber',
            'detail' => 'DNS resolves, but HTTPS was not detected for this domain.',
            'public_url' => 'http://' . $normalizedDomain,
        ];
    }

    private function checkHttpsAvailability(string $domain): bool
    {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'capture_peer_cert' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            'ssl://' . $domain . ':443',
            $errorNumber,
            $errorMessage,
            2,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (is_resource($socket)) {
            fclose($socket);

            return true;
        }

        return false;
    }

    private function forgetDomainStatusCache(?string $domain, ?string $host): void
    {
        $normalizedDomain = DomainHelper::normalize($domain);

        if (!$normalizedDomain) {
            return;
        }

        Cache::forget('school-domain-network:' . md5($normalizedDomain));
    }

    private function ensureSuperAdmin(): void
    {
        $user = auth()->user();

        if (!$user || ($user->role?->value ?? null) !== UserRole::SUPER_ADMIN->value) {
            abort(403, 'Unauthorized.');
        }
    }
}
