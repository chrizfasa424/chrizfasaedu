<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
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

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
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
