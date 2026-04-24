<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectPortalUsersFromAdmin
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        // Targeted fix: never bounce /staff to admin login when web guard is missing.
        if (!$request->is('staff') && !$request->is('staff/*')) {
            return $next($request);
        }

        $portalGuard = Auth::guard('portal');
        $portalUser = $portalGuard->user();

        if (!$portalUser && method_exists($portalGuard, 'getName')) {
            $portalSessionKey = $portalGuard->getName();
            $portalUserId = $request->session()->get($portalSessionKey);

            if ($portalUserId) {
                $portalUser = User::query()->find((int) $portalUserId);
            }
        }

        if (!$portalUser) {
            return $next($request);
        }

        $role = (string) ($portalUser->role?->value ?? $portalUser->role ?? '');

        $target = match ($role) {
            'student' => route('student.dashboard'),
            'parent' => route('parent.dashboard'),
            default => null,
        };

        if (!$target) {
            return $next($request);
        }

        return redirect()->to($target);
    }
}
