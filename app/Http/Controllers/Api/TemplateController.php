<?php

namespace App\Http\Controllers\Api;

use App\Services\Abstract\TemplateServiceInterface;
use App\Http\Requests\Template\CreateTemplateRequest;
use App\Http\Requests\Template\UpdateTemplateRequest;
use App\Dtos\TemplateDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Template Management
 *
 * APIs for managing templates
 */
class TemplateController extends Controller
{
    public function __construct(
        protected TemplateServiceInterface $templateService
    ) {}

    /**
     * List Templates
     *
     * Get a paginated list of all public templates.
     * @responseFile storage/app/private/scribe/responses/templates.index.json
     */
    public function index(): JsonResponse
    {
        $result = $this->templateService->getAll();
        return $result->toResponse();
    }

    /**
     * Get Public Templates
     *
     * Get a list of all public templates.
     * @responseFile storage/app/private/scribe/responses/templates.index.json
     */
    public function publicTemplates(): JsonResponse
    {
        $result = $this->templateService->getPublicTemplates();
        return $result->toResponse();
    }

    /**
     * Get My Templates
     *
     * Get a list of templates created by the authenticated user.
     * @authenticated
     * @responseFile storage/app/private/scribe/responses/templates.index.json
     */
    public function myTemplates(): JsonResponse
    {
        $result = $this->templateService->getByUser(Auth::id());
        return $result->toResponse();
    }

    /**
     * Create Template
     *
     * Create a new survey template.
     * @authenticated
     * @responseFile status=201 storage/app/private/scribe/responses/templates.show.json
     */
    public function store(CreateTemplateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        
        $dto = TemplateDto::fromArray($data);
        $result = $this->templateService->create($dto);
        return $result->toResponse();
    }

    /**
     * Get Template
     *
     * Get the details of a specific template.
     * @urlParam id required The ID of the template. Example: 1
     * @responseFile storage/app/private/scribe/responses/templates.show.json
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->templateService->find($id);
        
        if ($result->getData() === null) {
            return $result->toResponse();
        }

        $this->authorize('view', $result->getData());
        return $result->toResponse();
    }

    /**
     * Update Template
     *
     * Update a specific template.
     * @authenticated
     * @urlParam id required The ID of the template. Example: 1
     * @responseFile storage/app/private/scribe/responses/templates.show.json
     */
    public function update(UpdateTemplateRequest $request, int $id): JsonResponse
    {
        $template = $this->templateService->find($id)->getData();
        $this->authorize('update', $template);

        $result = $this->templateService->update($id, TemplateDto::fromArray($request->validated()));
        return $result->toResponse();
    }

    /**
     * Delete Template
     *
     * Delete a specific template.
     * @authenticated
     * @urlParam id required The ID of the template. Example: 1
     * @response 200 {"success": true, "message": "Template deleted successfully", "data": null}
     */
    public function destroy(int $id): JsonResponse
    {
        $template = $this->templateService->find($id)->getData();
        $this->authorize('delete', $template);

        $result = $this->templateService->delete($id);
        return $result->toResponse();
    }

    /**
     * Fork Template
     *
     * Create a copy of an existing template for the authenticated user.
     * @authenticated
     * @urlParam id required The ID of the template to fork. Example: 1
     * @responseFile status=201 storage/app/private/scribe/responses/templates.show.json
     */
    public function fork(int $id): JsonResponse
    {
        $template = $this->templateService->find($id)->getData();
        $this->authorize('fork', $template);

        $result = $this->templateService->fork($id, Auth::id());
        return $result->toResponse();
    }

    // Template Version Management
    /**
     * Get Template Versions
     *
     * Get all versions of a specific template.
     * @authenticated
     * @urlParam id required The ID of the template. Example: 1
     * @responseFile storage/app/private/scribe/responses/template_versions.index.json
     */
    public function versions(int $id): JsonResponse
    {
        $result = $this->templateService->getVersions($id);
        return $result->toResponse();
    }

    /**
     * Create Template Version
     *
     * Create a new version of a template.
     * @authenticated
     * @urlParam id required The ID of the template. Example: 1
     * @responseFile status=201 storage/app/private/scribe/responses/template_versions.show.json
     */
    public function createVersion(int $id): JsonResponse
    {
        $result = $this->templateService->createVersion($id, Auth::id());
        return $result->toResponse();
    }

    /**
     * Restore Template Version
     *
     * Restore a template to a specific version.
     * @authenticated
     * @urlParam id required The ID of the template. Example: 1
     * @urlParam versionId required The ID of the version to restore. Example: 2
     * @responseFile storage/app/private/scribe/responses/templates.show.json
     */
    public function restoreVersion(int $id, int $versionId): JsonResponse
    {
        $result = $this->templateService->restoreVersion($id, $versionId, Auth::id());
        return $result->toResponse();
    }
} 