<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web');

        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        if (
            $request->routeIs('profile.show')
            || $request->routeIs('profile.change.password')
            || $request->routeIs('logout')
        ) {
            return $next($request);
        }

        return redirect()
            ->route('profile.show', ['tab' => 'password'])
            ->with('error', 'You must change your temporary password before continuing.');
    }
}

