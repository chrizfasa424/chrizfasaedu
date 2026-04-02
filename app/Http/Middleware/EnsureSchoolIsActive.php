<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSchoolIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->school && !$user->school->isSubscriptionActive()) {
            if (!$user->isSuperAdmin()) {
                return redirect('/subscription-expired');
            }
        }

        return $next($request);
    }
}
