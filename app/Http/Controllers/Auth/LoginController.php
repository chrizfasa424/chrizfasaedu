<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath($user));
        }

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
        return match(true) {
            $user->isSuperAdmin() => '/admin/dashboard',
            $user->isSchoolAdmin() => '/dashboard',
            $user->isTeacher() => '/teacher/dashboard',
            $user->isStudent() => '/student/dashboard',
            $user->isParent() => '/parent/dashboard',
            default => '/dashboard',
        };
    }
}
