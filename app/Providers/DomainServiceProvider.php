<?php

namespace App\Providers;

use App\Services\Abstract\AuthServiceInterface;
use App\Services\Concrete\AuthService;
use App\Services\Abstract\TemplateServiceInterface;
use App\Services\Concrete\TemplateService;
use App\Services\Abstract\SurveyServiceInterface;
use App\Services\Concrete\SurveyService;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TemplateServiceInterface::class, TemplateService::class);
        $this->app->bind(SurveyServiceInterface::class, SurveyService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
} 