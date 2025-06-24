<?php

namespace App\Providers;

use App\Repositories\Abstract\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Abstract\TemplateRepositoryInterface;
use App\Repositories\Eloquent\TemplateRepository;
use App\Repositories\Abstract\TemplateVersionRepositoryInterface;
use App\Repositories\Eloquent\TemplateVersionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
        $this->app->bind(TemplateVersionRepositoryInterface::class, TemplateVersionRepository::class);
    }

    public function boot()
    {
        
    }
}
