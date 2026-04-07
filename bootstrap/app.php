<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'school.active' => \App\Http\Middleware\EnsureSchoolIsActive::class,
            'portal.guard' => \App\Http\Middleware\UsePortalGuard::class,
        ]);

        // Redirect unauthenticated portal guard requests to /portal instead of /login
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->routeIs('student.*') || $request->routeIs('parent.*')) {
                return route('portal.login');
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
