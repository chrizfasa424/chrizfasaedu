<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PublicPageContent;
use App\Support\SchoolContext;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    private const PORTAL_RESET_LINK_STATUS = 'If your portal account is eligible, a password reset link will be sent to that email.';
    private const INVALID_PORTAL_RESET_CREDENTIALS = 'Invalid credentials.';

    // ── Admin: Forgot Password ─────────────────────────────
    public function showAdminForgotForm()
    {
        return view('auth.passwords.admin-forgot');
    }

    public function sendAdminResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('users')->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Password reset link sent! Check your email.')
            : back()->withErrors(['email' => __($status)]);
    }

    // ── Admin: Reset Password ──────────────────────────────
    public function showAdminResetForm(Request $request, string $token)
    {
        return view('auth.passwords.admin-reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetAdminPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password reset successfully. Please sign in.')
            : back()->withErrors(['email' => __($status)]);
    }

    // ── Portal: Forgot Password ────────────────────────────
    public function showPortalForgotForm(Request $request)
    {
        $school     = SchoolContext::current($request);
        $publicPage = PublicPageContent::forSchool($school);
        $primary    = trim((string) ($publicPage['primary_color']   ?? '#059669'));
        $secondary  = trim((string) ($publicPage['secondary_color'] ?? '#ffffff'));
        $schoolName = $school?->name ?? config('app.name');

        return view('auth.passwords.portal-forgot', compact('primary', 'secondary', 'schoolName'));
    }

    public function sendPortalResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $portalUser = User::query()
            ->whereRaw('LOWER(email) = ?', [Str::lower(trim((string) $request->input('email')))])
            ->first();

        if (!$this->canUsePortalReset($portalUser)) {
            return back()->with('status', self::PORTAL_RESET_LINK_STATUS);
        }

        Password::broker('users')->sendResetLink($request->only('email'));

        return back()->with('status', self::PORTAL_RESET_LINK_STATUS);
    }

    // ── Portal: Reset Password ─────────────────────────────
    public function showPortalResetForm(Request $request, string $token)
    {
        $school     = SchoolContext::current($request);
        $publicPage = PublicPageContent::forSchool($school);
        $primary    = trim((string) ($publicPage['primary_color']   ?? '#059669'));
        $secondary  = trim((string) ($publicPage['secondary_color'] ?? '#ffffff'));
        $schoolName = $school?->name ?? config('app.name');

        return view('auth.passwords.portal-reset', [
            'token'      => $token,
            'email'      => $request->email,
            'primary'    => $primary,
            'secondary'  => $secondary,
            'schoolName' => $schoolName,
        ]);
    }

    public function resetPortalPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $portalUser = User::query()
            ->whereRaw('LOWER(email) = ?', [Str::lower(trim((string) $request->input('email')))])
            ->first();

        if (!$this->canUsePortalReset($portalUser)) {
            return back()->withErrors(['email' => self::INVALID_PORTAL_RESET_CREDENTIALS])->onlyInput('email');
        }

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('portal.login')->with('status', 'Password reset successfully. Please sign in.')
            : back()->withErrors(['email' => self::INVALID_PORTAL_RESET_CREDENTIALS])->onlyInput('email');
    }

    private function canUsePortalReset(?User $user): bool
    {
        return $user
            && (bool) $user->is_active
            && ($user->isStudent() || $user->isParent());
    }
}
