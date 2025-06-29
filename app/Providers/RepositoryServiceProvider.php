<?php

namespace App\Providers;

use App\Core\BaseRepository;
use App\Core\BaseRepositoryInterface;
use App\Repositories\Abstract\AnswerRepositoryInterface;
use App\Repositories\Abstract\ChoiceRepositoryInterface;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use App\Repositories\Abstract\ResponseRepositoryInterface;
use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Repositories\Abstract\TemplateRepositoryInterface;
use App\Repositories\Abstract\TemplateVersionRepositoryInterface;
use App\Repositories\Abstract\UserRepositoryInterface;
use App\Repositories\Abstract\RoleRepositoryInterface;
use App\Repositories\Eloquent\AnswerRepository;
use App\Repositories\Eloquent\ChoiceRepository;
use App\Repositories\Eloquent\QuestionRepository;
use App\Repositories\Eloquent\ResponseRepository;
use App\Repositories\Eloquent\SurveyPageRepository;
use App\Repositories\Eloquent\SurveyRepository;
use App\Repositories\Eloquent\TemplateRepository;
use App\Repositories\Eloquent\TemplateVersionRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\RoleRepository;
use App\Services\Abstract\AuthServiceInterface;
use App\Services\Abstract\ChoiceServiceInterface;
use App\Services\Abstract\QuestionServiceInterface;
use App\Services\Abstract\ResponseServiceInterface;
use App\Services\Abstract\SurveyPageServiceInterface;
use App\Services\Abstract\SurveyServiceInterface;
use App\Services\Abstract\TemplateServiceInterface;
use App\Services\Concrete\AuthService;
use App\Services\Concrete\ChoiceService;
use App\Services\Concrete\QuestionService;
use App\Services\Concrete\ResponseService;
use App\Services\Concrete\SurveyPageService;
use App\Services\Concrete\SurveyService;
use App\Services\Concrete\TemplateService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
        $this->app->bind(TemplateVersionRepositoryInterface::class, TemplateVersionRepository::class);
        $this->app->bind(SurveyRepositoryInterface::class, SurveyRepository::class);
        $this->app->bind(SurveyPageRepositoryInterface::class, SurveyPageRepository::class);
        $this->app->bind(ChoiceRepositoryInterface::class, ChoiceRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        $this->app->bind(ResponseRepositoryInterface::class, ResponseRepository::class);
        $this->app->bind(AnswerRepositoryInterface::class, AnswerRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TemplateServiceInterface::class, TemplateService::class);
        $this->app->bind(SurveyServiceInterface::class, SurveyService::class);
        $this->app->bind(SurveyPageServiceInterface::class, SurveyPageService::class);
        $this->app->bind(QuestionServiceInterface::class, QuestionService::class);
        $this->app->bind(ChoiceServiceInterface::class, ChoiceService::class);
        $this->app->bind(ResponseServiceInterface::class, ResponseService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
