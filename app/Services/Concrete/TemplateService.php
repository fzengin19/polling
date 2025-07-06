<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\TemplateRepositoryInterface;
use App\Repositories\Abstract\TemplateVersionRepositoryInterface;
use App\Services\Abstract\TemplateServiceInterface;
use App\Dtos\TemplateDto;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TemplateService implements TemplateServiceInterface
{
    public function __construct(
        protected TemplateRepositoryInterface $templateRepository,
        protected TemplateVersionRepositoryInterface $templateVersionRepository,
        protected ResourceMapInterface $resourceMap
    ) {}

    public function create(TemplateDto $dto): ServiceResponse
    {
        $template = $this->templateRepository->create($dto->toDatabaseArray());
        return ServiceResponse::created($template, 'Template created successfully.');
    }

    public function update(int $id, TemplateDto $dto): ServiceResponse
    {
        $this->templateRepository->update($id, $dto->toDatabaseArray());
        return ServiceResponse::success($this->templateRepository->find($id));
    }

    public function delete(int $id): ServiceResponse
    {
        $this->templateRepository->delete($id);
        return ServiceResponse::noContent('Template deleted successfully.');
    }

    public function find(int $id): ServiceResponse
    {
        $template = $this->templateRepository->find($id);
        if (!$template) {
            return ServiceResponse::notFound('Template not found.');
        }
        return ServiceResponse::success($template);
    }

    public function getAll(): ServiceResponse
    {
        $templates = $this->templateRepository->all();
        return ServiceResponse::success($templates);
    }

    public function getByUser(int $userId): ServiceResponse
    {
        $templates = $this->templateRepository->findByUser($userId);
        return ServiceResponse::success($templates);
    }

    public function getPublicTemplates(): ServiceResponse
    {
        $templates = $this->templateRepository->getPublicTemplates();
        return ServiceResponse::success($templates);
    }

    public function fork(int $templateId, int $userId): ServiceResponse
    {
        $originalTemplate = $this->templateRepository->find($templateId);
        if (!$originalTemplate) {
            return ServiceResponse::notFound('Template not found.');
        }

        $latestVersion = $this->templateVersionRepository->getLatestVersion($templateId);
        if (!$latestVersion) {
            return ServiceResponse::error('Cannot fork a template with no versions.', null, 422);
        }

        $forkedTemplate = null;
        DB::transaction(function () use ($originalTemplate, $latestVersion, $userId, &$forkedTemplate) {
            $forkedTemplate = $this->templateRepository->create([
                'title' => $originalTemplate->title . ' (Fork)',
                'description' => $originalTemplate->description,
                'is_public' => false,
                'created_by' => $userId,
                'forked_from_template_id' => $originalTemplate->id,
            ]);

            $this->templateVersionRepository->create([
                'template_id' => $forkedTemplate->id,
                'version' => '1.0.0',
                'snapshot' => $latestVersion->snapshot,
            ]);
        });

        return ServiceResponse::created($forkedTemplate, 'Template forked successfully.');
    }

    public function getVersions(int $templateId): ServiceResponse
    {
        $versions = $this->templateVersionRepository->findByTemplate($templateId);
        return ServiceResponse::success($versions);
    }

    public function createVersion(int $templateId, int $userId): ServiceResponse
    {
        $template = $this->templateRepository->find($templateId);
        if (!$template) {
            return ServiceResponse::notFound('Template not found.');
        }

        $latestVersion = $this->templateVersionRepository->getLatestVersion($templateId);
        $newVersionNumber = $this->incrementVersion($latestVersion?->version);

        $latestSurvey = $template->surveys()->with('pages.questions.choices')->latest()->first();
        $snapshot = $latestSurvey ? $latestSurvey->toArray() : [
            'title' => $template->title,
            'description' => $template->description,
            'pages' => [],
        ];

        $version = $this->templateVersionRepository->create([
            'template_id' => $templateId,
            'version' => $newVersionNumber,
            'snapshot' => $snapshot,
        ]);

        return ServiceResponse::created($version);
    }

    public function restoreVersion(int $templateId, int $versionId, int $userId): ServiceResponse
    {
        $template = $this->templateRepository->find($templateId);
        if (!$template) {
            return ServiceResponse::notFound('Template not found.');
        }

        $version = $this->templateVersionRepository->find($versionId);
        if (!$version || $version->template_id !== $templateId) {
            return ServiceResponse::notFound('Version not found for this template.');
        }

        $snapshot = $version->snapshot;
        $template->update([
            'title' => $snapshot['title'],
            'description' => $snapshot['description'],
        ]);

        $survey = $template->surveys()->create([
            'title' => $snapshot['title'],
            'description' => $snapshot['description'],
            'created_by' => $userId,
            'status' => 'draft',
        ]);

        foreach ($snapshot['pages'] ?? [] as $pageData) {
            $page = $survey->pages()->create([
                'title' => $pageData['title'],
                'order_index' => $pageData['order_index'],
            ]);

            foreach ($pageData['questions'] ?? [] as $questionData) {
                $question = $page->questions()->create([
                    'title' => $questionData['title'],
                    'type' => $questionData['type'],
                    'is_required' => $questionData['is_required'],
                    'config' => $questionData['config'],
                    'order_index' => $questionData['order_index'],
                ]);

                foreach ($questionData['choices'] ?? [] as $choiceData) {
                    $question->choices()->create([
                        'label' => $choiceData['label'],
                        'value' => $choiceData['value'],
                        'order_index' => $choiceData['order_index'],
                    ]);
                }
            }
        }

        return ServiceResponse::success($template->fresh());
    }

    private function incrementVersion(?string $version): string
    {
        if (!$version) {
            return '1.0.0';
        }
        $parts = explode('.', $version);
        $parts[2] = (int)$parts[2] + 1;
        return implode('.', $parts);
    }
} 