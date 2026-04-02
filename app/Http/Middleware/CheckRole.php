<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role->value, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
