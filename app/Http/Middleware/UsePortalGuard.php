<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsePortalGuard
{
    public function handle(Request $request, Closure $next): mixed
    {
        Auth::shouldUse('portal');
        return $next($request);
    }
}
