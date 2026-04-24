<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\TrustProxies::class);
        $middleware->append(\App\Http\Middleware\AddSecurityHeaders::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'school.active' => \App\Http\Middleware\EnsureSchoolIsActive::class,
            'portal.guard' => \App\Http\Middleware\UsePortalGuard::class,
            'redirect.portal.from.admin' => \App\Http\Middleware\RedirectPortalUsersFromAdmin::class,
            'force.password.change' => \App\Http\Middleware\ForcePasswordChange::class,
        ]);

        // Redirect unauthenticated portal guard requests to /portal instead of /login
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            $webUser = \Illuminate\Support\Facades\Auth::guard('web')->user();
            $portalUser = \Illuminate\Support\Facades\Auth::guard('portal')->user();

            $isPortalArea = (
                $request->routeIs('portal.*')
                || $request->routeIs('student.*')
                || $request->routeIs('parent.*')
                || $request->is('portal')
                || $request->is('portal/*')
                || $request->is('student/*')
                || $request->is('parent/*')
                || $request->is('my/*')
            );

            $resolveDashboard = function ($user): string {
                $role = (string) ($user?->role?->value ?? $user?->role ?? '');

                return match ($role) {
                    'student' => route('student.dashboard'),
                    'parent' => route('parent.dashboard'),
                    'teacher' => route('teacher.dashboard'),
                    'staff' => route('staff.dashboard'),
                    default => route('dashboard'),
                };
            };

            if ($isPortalArea) {
                if ($webUser) {
                    return $resolveDashboard($webUser);
                }
                return route('portal.login');
            }

            if ($request->routeIs('staff.dashboard') || $request->is('staff/dashboard')) {
                return route('staff.login');
            }

            // If a portal session exists and user enters admin URLs, return to their portal dashboard.
            if ($portalUser) {
                return $resolveDashboard($portalUser);
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TokenMismatchException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired. Please refresh and try again.',
                ], 419);
            }

            return redirect()
                ->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->with('error', 'Your session expired. Please refresh and try again.');
        });
    })->create();
