<?php

namespace App\Providers;

use App\Repositories\Abstract\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Abstract\TemplateRepositoryInterface;
use App\Repositories\Eloquent\TemplateRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
    }

    public function boot()
    {
        
    }
}
