<?php

namespace App\Providers;

use App\Dtos\UserIdentityDto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserIdentityDto::class, function ($app) {
            $userId = Auth::id();
            $anonId = Request::cookie('anon_id');

            return new UserIdentityDto($userId, $anonId);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
