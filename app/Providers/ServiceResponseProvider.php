<?php

namespace App\Providers;

use App\Responses\Abstract\ResourceMapInterface;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\RoleServiceInterface;
use App\Services\Concrete\RoleService;
use Illuminate\Support\ServiceProvider;

class ServiceResponseProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ResourceMapInterface::class, ApiResourceMap::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 