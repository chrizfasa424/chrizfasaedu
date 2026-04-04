<?php

namespace App\Providers;

use App\Support\SchoolContext;
use App\Services\InvoiceService;
use App\Services\PaystackService;
use App\Services\ResultService;
use App\Services\SmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ResultService::class);
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(SmsService::class);
        $this->app->singleton(PaystackService::class);
    }

    public function boot(): void
    {
        if (auth()->check()) {
            SchoolContext::ensureUserSchool(auth()->user());
        }
    }
}
