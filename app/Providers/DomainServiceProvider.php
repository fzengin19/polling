<?php

namespace App\Providers;

use App\Services\Abstract\AuthServiceInterface;
use App\Services\Concrete\AuthService;
use App\Services\Abstract\TemplateServiceInterface;
use App\Services\Concrete\TemplateService;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
} 