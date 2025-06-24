<?php

namespace App\Providers;

use App\Repositories\Abstract\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Abstract\TemplateRepositoryInterface;
use App\Repositories\Eloquent\TemplateRepository;
use App\Repositories\Abstract\TemplateVersionRepositoryInterface;
use App\Repositories\Eloquent\TemplateVersionRepository;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Repositories\Eloquent\SurveyRepository;
use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use App\Repositories\Eloquent\SurveyPageRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
        $this->app->bind(TemplateVersionRepositoryInterface::class, TemplateVersionRepository::class);
        $this->app->bind(SurveyRepositoryInterface::class, SurveyRepository::class);
        $this->app->bind(SurveyPageRepositoryInterface::class, SurveyPageRepository::class);
        $this->app->bind(
            \App\Repositories\Abstract\QuestionRepositoryInterface::class,
            \App\Repositories\Eloquent\QuestionRepository::class
        );
    }

    public function boot()
    {
        
    }
}
