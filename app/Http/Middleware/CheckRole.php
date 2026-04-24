<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();
        $role = (string) ($user?->role?->value ?? $user?->role ?? '');

        if (!$user || !in_array($role, $roles, true)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
