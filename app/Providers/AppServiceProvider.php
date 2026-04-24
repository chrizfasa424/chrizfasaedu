<?php

namespace App\Providers;

use App\Support\SchoolContext;
use App\Services\AssignmentService;
use App\Services\AssignmentSubmissionService;
use App\Services\InvoiceService;
use App\Services\PaystackService;
use App\Services\ResultService;
use App\Services\ResultImportService;
use App\Services\ResultImportValidatorService;
use App\Services\ResultSubmissionService;
use App\Services\ResultSheetRankingService;
use App\Services\ResultTemplateService;
use App\Services\GradingService;
use App\Services\StaffClassAuthorizationService;
use App\Services\SmsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ResultService::class);
        $this->app->singleton(ResultImportService::class);
        $this->app->singleton(ResultImportValidatorService::class);
        $this->app->singleton(ResultSubmissionService::class);
        $this->app->singleton(ResultSheetRankingService::class);
        $this->app->singleton(ResultTemplateService::class);
        $this->app->singleton(GradingService::class);
        $this->app->singleton(StaffClassAuthorizationService::class);
        $this->app->singleton(AssignmentService::class);
        $this->app->singleton(AssignmentSubmissionService::class);
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(SmsService::class);
        $this->app->singleton(PaystackService::class);
    }

    public function boot(): void
    {
        $forceHttps = filter_var(env('APP_FORCE_HTTPS', true), FILTER_VALIDATE_BOOL);

        if ($forceHttps && app()->environment('production') && str_starts_with((string) config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }

        if (auth()->check()) {
            SchoolContext::ensureUserSchool(auth()->user());
        }
    }
}
