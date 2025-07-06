<?php

namespace App\Providers;

use App\Services\Abstract\AuthServiceInterface;
use App\Services\Concrete\AuthService;
use App\Services\Abstract\TemplateServiceInterface;
use App\Services\Concrete\TemplateService;
use App\Services\Abstract\SurveyServiceInterface;
use App\Services\Concrete\SurveyService;
use App\Services\Abstract\SurveyPageServiceInterface;
use App\Services\Concrete\SurveyPageService;
use App\Services\Abstract\QuestionServiceInterface;
use App\Services\Concrete\QuestionService;
use App\Services\Abstract\RoleServiceInterface;
use App\Services\Concrete\RoleService;
use App\Services\Abstract\MediaServiceInterface;
use App\Services\Concrete\MediaService;
use App\Services\Abstract\ChoiceServiceInterface;
use App\Services\Concrete\ChoiceService;
use App\Services\Abstract\ResponseServiceInterface;
use App\Services\Concrete\ResponseService;
use App\Services\Abstract\UserServiceInterface;
use App\Services\Concrete\UserService;
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
        $this->app->bind(SurveyPageServiceInterface::class, SurveyPageService::class);
        $this->app->bind(QuestionServiceInterface::class, QuestionService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(MediaServiceInterface::class, MediaService::class);
        $this->app->bind(ChoiceServiceInterface::class, ChoiceService::class);
        $this->app->bind(ResponseServiceInterface::class, ResponseService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
} 