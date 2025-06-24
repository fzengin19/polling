<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\TemplateRepositoryInterface;
use App\Repositories\Abstract\TemplateVersionRepositoryInterface;
use App\Services\Abstract\TemplateServiceInterface;
use App\Dtos\TemplateDto;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TemplateService implements TemplateServiceInterface
{
    public function __construct(
        protected TemplateRepositoryInterface $templateRepository,
        protected TemplateVersionRepositoryInterface $templateVersionRepository,
        protected ResourceMapInterface $resourceMap
    ) {}

    public function create(TemplateDto $dto): ServiceResponse
    {
        $template = $this->templateRepository->create($dto->toArray());

        return new ServiceResponse($template, $this->resourceMap, 201);
    }

    public function update(int $id, TemplateDto $dto): ServiceResponse
    {
        $template = $this->templateRepository->find($id);

        if (!$template) {
            throw new ModelNotFoundException('Template not found.');
        }

        // Check if user owns this template
        if ($template->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $this->templateRepository->update($template, $dto->toArray());

        return new ServiceResponse($template->fresh(), $this->resourceMap);
    }

    public function delete(int $id): ServiceResponse
    {
        $template = $this->templateRepository->find($id);

        if (!$template) {
            throw new ModelNotFoundException('Template not found.');
        }

        // Check if user owns this template
        if ($template->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $this->templateRepository->delete($template);

        return new ServiceResponse(['message' => 'Template deleted successfully.'], $this->resourceMap);
    }

    public function find(int $id): ServiceResponse
    {
        $template = $this->templateRepository->find($id);

        if (!$template) {
            throw new ModelNotFoundException('Template not found.');
        }

        // Check if user can access this template (owner or public)
        if ($template->created_by !== Auth::id() && !$template->is_public) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        return new ServiceResponse($template, $this->resourceMap);
    }

    public function getAll(): ServiceResponse
    {
        $templates = $this->templateRepository->all();

        return new ServiceResponse($templates, $this->resourceMap);
    }

    public function getByUser(int $userId): ServiceResponse
    {
        $templates = $this->templateRepository->findByUser($userId);

        return new ServiceResponse($templates, $this->resourceMap);
    }

    public function getPublicTemplates(): ServiceResponse
    {
        $templates = $this->templateRepository->getPublicTemplates();

        return new ServiceResponse($templates, $this->resourceMap);
    }

    public function fork(int $templateId, int $userId): ServiceResponse
    {
        $originalTemplate = $this->templateRepository->find($templateId);

        if (!$originalTemplate) {
            throw new ModelNotFoundException('Template not found.');
        }

        // Check if user can fork this template (public or owner)
        if ($originalTemplate->created_by !== $userId && !$originalTemplate->is_public) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $forkedTemplate = $this->templateRepository->create([
            'title' => $originalTemplate->title . ' (Fork)',
            'description' => $originalTemplate->description,
            'is_public' => false, // Forked templates are private by default
            'created_by' => $userId,
            'forked_from_template_id' => $templateId,
        ]);

        return new ServiceResponse($forkedTemplate, $this->resourceMap, 201);
    }

    // Version Management Methods
    public function getVersions(int $templateId): ServiceResponse
    {
        $template = $this->templateRepository->find($templateId);

        if (!$template) {
            throw new ModelNotFoundException('Template not found.');
        }

        // Check if user can access this template (owner or public)
        if ($template->created_by !== Auth::id() && !$template->is_public) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $versions = $this->templateVersionRepository->findByTemplate($templateId);

        return new ServiceResponse($versions, $this->resourceMap);
    }

    public function createVersion(int $templateId, int $userId): ServiceResponse
    {
        $template = $this->templateRepository->find($templateId);

        if (!$template) {
            throw new ModelNotFoundException('Template not found.');
        }

        // Check if user owns this template
        if ($template->created_by !== $userId) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        // Get latest version number
        $latestVersion = $this->templateVersionRepository->getLatestVersion($templateId);
        $versionNumber = $latestVersion ? $this->incrementVersion($latestVersion->version) : '1.0.0';

        // Create snapshot of current template state
        $snapshot = [
            'title' => $template->title,
            'description' => $template->description,
            'is_public' => $template->is_public,
            'created_at' => $template->created_at,
            'updated_at' => $template->updated_at,
        ];

        $version = $this->templateVersionRepository->create([
            'template_id' => $templateId,
            'version' => $versionNumber,
            'snapshot' => $snapshot,
        ]);

        return new ServiceResponse($version, $this->resourceMap, 201);
    }

    public function restoreVersion(int $templateId, int $versionId, int $userId): ServiceResponse
    {
        $template = $this->templateRepository->find($templateId);

        if (!$template) {
            throw new ModelNotFoundException('Template not found.');
        }

        // Check if user owns this template
        if ($template->created_by !== $userId) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $version = $this->templateVersionRepository->find($versionId);

        if (!$version || $version->template_id !== $templateId) {
            throw new ModelNotFoundException('Version not found.');
        }

        // Restore template from version snapshot
        $this->templateRepository->update($template, $version->snapshot);

        return new ServiceResponse($template->fresh(), $this->resourceMap);
    }

    /**
     * Increment semantic version
     */
    private function incrementVersion(string $version): string
    {
        $parts = explode('.', $version);
        $parts[2] = (int)$parts[2] + 1; // Increment patch version
        return implode('.', $parts);
    }
} 