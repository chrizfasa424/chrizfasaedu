<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\PublicPageContent;
use App\Support\SchoolContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
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

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Admin/staff login uses the default 'web' guard
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            SchoolContext::ensureUserSchool($user);
            $user->refresh();
            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath($user));
        }

        $this->logFailedLoginAttempt($request, $credentials['email']);

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function portalLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Student/parent login uses the 'portal' guard (separate session cookie)
        if (Auth::guard('portal')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('portal')->user();
            SchoolContext::ensureUserSchool($user);
            $user->refresh();
            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath($user));
        }

        $this->logFailedLoginAttempt($request, $credentials['email']);

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function portalLogout(Request $request)
    {
        Auth::guard('portal')->logout();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
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
            $user->isStudent() => '/student/dashboard',
            $user->isParent() => '/parent/dashboard',
            default => '/dashboard',
        };
    }

    private function logFailedLoginAttempt(Request $request, string $email): void
    {
        $school = SchoolContext::current($request);
        $knownUser = User::query()->where('email', $email)->first();

        AuditLog::query()->create([
            'school_id' => $school?->id,
            'user_id' => $knownUser?->id,
            'action' => 'failed_login',
            'model_type' => User::class,
            'model_id' => $knownUser?->id,
            'changes' => [
                'email' => Str::lower(trim($email)),
                'reason' => 'Invalid credentials',
                'path' => $request->path(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);
    }
}
