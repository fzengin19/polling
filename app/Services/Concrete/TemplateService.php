<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\TemplateRepositoryInterface;
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
} 