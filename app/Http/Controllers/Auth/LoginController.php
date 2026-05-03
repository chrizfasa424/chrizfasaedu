<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\PublicPageContent;
use App\Support\SchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login', [
            'loginMode' => 'admin',
            'loginAction' => route('login'),
            'pageTitle' => 'Admin Login',
            'primaryCtaLabel' => 'Sign In to Admin Console',
            'panelLabel' => 'Admin Area',
            'credentialHint' => 'Use your administrator account credentials to continue.',
        ]);
    }

    public function showStaffLoginForm()
    {
        return view('auth.login', [
            'loginMode' => 'staff',
            'loginAction' => route('staff.login.submit'),
            'pageTitle' => 'Staff Login',
            'primaryCtaLabel' => 'Sign In to Staff Dashboard',
            'panelLabel' => 'Staff Area',
            'credentialHint' => 'Use your staff account credentials to continue.',
        ]);
    }

    public function showPortalLoginForm(Request $request)
    {
        $school     = SchoolContext::current($request);
        $publicPage = PublicPageContent::forSchool($school);
        $primary    = trim((string) ($publicPage['primary_color']   ?? '#059669'));
        $secondary  = trim((string) ($publicPage['secondary_color'] ?? '#ffffff'));
        $schoolName = $school?->name ?? config('app.name');

        return view('auth.portal-login', compact('primary', 'secondary', 'schoolName'));
    }

    public function login(Request $request, string $loginMode = 'admin')
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        $credentials['email'] = Str::lower(trim((string) $credentials['email']));
        $allowedRoles = $loginMode === 'staff'
            ? $this->staffWebRoles()
            : $this->adminWebRoles();

        // Admin/staff login uses the default 'web' guard, scoped by role group.
        if (Auth::guard('web')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
            'role' => $allowedRoles,
        ], $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            SchoolContext::ensureUserSchool($user);
            $user->refresh();
            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
            $request->session()->regenerate();
            $request->session()->put('auth.login_mode', $loginMode === 'staff' ? 'staff' : 'admin');
            return redirect()->intended($this->redirectPath($user));
        }

        $account = User::query()
            ->whereRaw('LOWER(email) = ?', [$credentials['email']])
            ->first();
        $message = 'Invalid credentials.';

        if ($account && (bool) $account->is_active) {
            $role = (string) ($account->role?->value ?? $account->role ?? '');
            $staffRoles = $this->staffWebRoles();
            $adminRoles = $this->adminWebRoles();

            if ($loginMode === 'admin' && in_array($role, $staffRoles, true)) {
                $message = 'This is a staff account. Please sign in from the Staff Login page.';
            } elseif ($loginMode === 'staff' && in_array($role, $adminRoles, true)) {
                $message = 'This is an admin account. Please sign in from the Admin Login page.';
            } elseif (in_array($role, [UserRole::STUDENT->value, UserRole::PARENT->value], true)) {
                $message = 'This is a student/parent account. Please sign in from the Portal Login page.';
            }
        }

        $this->logFailedLoginAttempt($request, $credentials['email']);

        return back()->withErrors(['email' => $message])->onlyInput('email');
    }

    public function staffLogin(Request $request)
    {
        return $this->login($request, 'staff');
    }

    public function portalLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        $credentials['email'] = Str::lower(trim((string) $credentials['email']));

        // Student/parent login uses the 'portal' guard (separate session cookie)
        $portalCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
            'role' => [
                UserRole::STUDENT->value,
                UserRole::PARENT->value,
            ],
        ];

        if (Auth::guard('portal')->attempt($portalCredentials, $request->boolean('remember'))) {
            $user = Auth::guard('portal')->user();
            SchoolContext::ensureUserSchool($user);
            $user->refresh();
            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath($user));
        }

        $account = User::withTrashed()
            ->whereRaw('LOWER(email) = ?', [$credentials['email']])
            ->first();

        $message = 'Invalid credentials.';
        $failureReason = 'Invalid credentials';

        if ($account) {
            $role = (string) ($account->role?->value ?? $account->role ?? '');
            $adminRoles = $this->adminWebRoles();
            $staffRoles = $this->staffWebRoles();

            if ($account->trashed()) {
                $message = 'This account has been archived. Please contact the school admin.';
                $failureReason = 'Archived account';
            } elseif (!(bool) $account->is_active) {
                $message = 'This account is inactive. Please contact the school admin.';
                $failureReason = 'Inactive account';
            } elseif (in_array($role, $adminRoles, true)) {
                $message = 'This is an admin account. Please sign in from the Admin Login page.';
                $failureReason = 'Wrong login panel (admin account)';
            } elseif (in_array($role, $staffRoles, true)) {
                $message = 'This is a staff account. Please sign in from the Staff Login page.';
                $failureReason = 'Wrong login panel (staff account)';
            } elseif (in_array($role, [UserRole::STUDENT->value, UserRole::PARENT->value], true)) {
                $message = 'Incorrect password. Please try again or use Forgot password.';
                $failureReason = 'Wrong password';
            }
        }

        $this->logFailedLoginAttempt($request, $credentials['email'], $failureReason);

        return back()->withErrors(['email' => $message])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        return $this->logoutWebUser($request, 'login');
    }

    public function staffLogout(Request $request)
    {
        return $this->logoutWebUser($request, 'staff.login');
    }

    private function logoutWebUser(Request $request, string $defaultRedirectRoute): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        $loginMode = (string) $request->session()->get('auth.login_mode', '');
        $staffFacingRoles = [
            UserRole::TEACHER->value,
            UserRole::STAFF->value,
            UserRole::ACCOUNTANT->value,
            UserRole::LIBRARIAN->value,
            UserRole::DRIVER->value,
            UserRole::NURSE->value,
        ];
        $roleValue = (string) ($user?->role?->value ?? $user?->role ?? '');

        $isStaffContext = $loginMode === 'staff' || in_array($roleValue, $staffFacingRoles, true);
        $redirectRoute = $isStaffContext ? 'staff.login' : $defaultRedirectRoute;

        $this->logoutAllGuards($request);

        return redirect()->route($redirectRoute);
    }

    public function portalLogout(Request $request)
    {
        $this->logoutAllGuards($request);
        return redirect()->route('portal.login');
    }

    private function logoutAllGuards(Request $request): void
    {
        Auth::guard('web')->logout();
        Auth::guard('portal')->logout();
        $request->session()->forget('auth.login_mode');
        $request->session()->migrate(true);
        $request->session()->regenerateToken();
    }

    protected function redirectPath($user): string
    {
        if (SchoolContext::isSingleSchoolMode() && ($user->isSuperAdmin() || $user->isSchoolAdmin())) {
            return '/dashboard';
        }

        return match(true) {
            $user->isSuperAdmin() => '/admin/dashboard',
            $user->isSchoolAdmin() => '/dashboard',
            $user->isTeacher() => '/teacher/dashboard',
            $user->isStaff() => '/staff/dashboard',
            $user->isStudent() => '/student/dashboard',
            $user->isParent() => '/parent/dashboard',
            default => '/dashboard',
        };
    }

    private function logFailedLoginAttempt(Request $request, string $email, string $reason = 'Invalid credentials'): void
    {
        $school = SchoolContext::current($request);
        $knownUser = User::withTrashed()->whereRaw('LOWER(email) = ?', [Str::lower(trim($email))])->first();

        AuditLog::query()->create([
            'school_id' => $school?->id,
            'user_id' => $knownUser?->id,
            'action' => 'failed_login',
            'model_type' => User::class,
            'model_id' => $knownUser?->id,
            'changes' => [
                'email' => Str::lower(trim($email)),
                'reason' => $reason,
                'path' => $request->path(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);
    }

    /**
     * Roles allowed in the admin login panel.
     *
     * @return array<int, string>
     */
    private function adminWebRoles(): array
    {
        return [
            UserRole::SUPER_ADMIN->value,
            UserRole::SCHOOL_ADMIN->value,
            UserRole::PRINCIPAL->value,
            UserRole::VICE_PRINCIPAL->value,
        ];
    }

    /**
     * Roles allowed in the staff login panel.
     *
     * @return array<int, string>
     */
    private function staffWebRoles(): array
    {
        return [
            UserRole::TEACHER->value,
            UserRole::STAFF->value,
            UserRole::ACCOUNTANT->value,
            UserRole::LIBRARIAN->value,
            UserRole::DRIVER->value,
            UserRole::NURSE->value,
        ];
    }
}
