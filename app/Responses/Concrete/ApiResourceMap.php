<?php

namespace App\Responses\Concrete;

use App\Http\Resources\TemplateResource;
use App\Http\Resources\TemplateVersionResource;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyPageResource;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Str;

class ApiResourceMap implements ResourceMapInterface
{
    private array $map = [
        'template' => TemplateResource::class,
        'templates' => TemplateResource::class,
        'template_version' => TemplateVersionResource::class,
        'template_versions' => TemplateVersionResource::class,
        'survey' => SurveyResource::class,
        'surveys' => SurveyResource::class,
        'survey_page' => SurveyPageResource::class,
        'survey_pages' => SurveyPageResource::class,
        'question' => \App\Http\Resources\QuestionResource::class,
        'questions' => \App\Http\Resources\QuestionResource::class,
        'choice' => \App\Http\Resources\ChoiceResource::class,
        'choices' => \App\Http\Resources\ChoiceResource::class,
    ];

    public function resolve(string $key): ?string
    {
        $singular = Str::singular($key);
        $plural = Str::plural($key);

        return $this->map[$key]
            ?? $this->map[$singular]
            ?? $this->map[$plural]
            ?? null;
    }
}
