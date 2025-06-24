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

class TemplateController extends Controller
{
    public function __construct(
        protected TemplateServiceInterface $templateService
    ) {}

    public function index(): JsonResponse
    {
        $result = $this->templateService->getAll();
        return $result->toResponse();
    }

    public function store(CreateTemplateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        
        $dto = TemplateDto::fromArray($data);
        $result = $this->templateService->create($dto);
        return $result->toResponse();
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->templateService->find($id);
        return $result->toResponse();
    }

    public function update(UpdateTemplateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        
        $dto = TemplateDto::fromArray($data);
        $result = $this->templateService->update($id, $dto);
        return $result->toResponse();
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->templateService->delete($id);
        return $result->toResponse();
    }

    public function myTemplates(): JsonResponse
    {
        $result = $this->templateService->getByUser(Auth::id());
        return $result->toResponse();
    }

    public function publicTemplates(): JsonResponse
    {
        $result = $this->templateService->getPublicTemplates();
        return $result->toResponse();
    }

    public function fork(int $id): JsonResponse
    {
        $result = $this->templateService->fork($id, Auth::id());
        return $result->toResponse();
    }

    // Template Version Management
    public function versions(int $id): JsonResponse
    {
        $result = $this->templateService->getVersions($id);
        return $result->toResponse();
    }

    public function createVersion(int $id): JsonResponse
    {
        $result = $this->templateService->createVersion($id, Auth::id());
        return $result->toResponse();
    }

    public function restoreVersion(int $id, int $versionId): JsonResponse
    {
        $result = $this->templateService->restoreVersion($id, $versionId, Auth::id());
        return $result->toResponse();
    }
} 