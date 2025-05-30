<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Abstract\PollRepositoryInterface;
use App\Repositories\Eloquent\PollRepository;

use App\Repositories\Abstract\OptionRepositoryInterface;
use App\Repositories\Eloquent\OptionRepository;

use App\Repositories\Abstract\PollVoteRepositoryInterface;
use App\Repositories\Eloquent\PollVoteRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PollRepositoryInterface::class, PollRepository::class);
        $this->app->bind(OptionRepositoryInterface::class, OptionRepository::class);
        $this->app->bind(PollVoteRepositoryInterface::class, PollVoteRepository::class);
    }

    public function boot()
    {
        
    }
}
