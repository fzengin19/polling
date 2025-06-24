<?php

namespace App\Http\Controllers\Api;

use App\Services\Abstract\RoleServiceInterface;
use App\Http\Requests\Role\AssignRoleRequest;
use App\Http\Requests\Role\RemoveRoleRequest;
use App\Dtos\RoleAssignmentDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        protected RoleServiceInterface $roleService
    ) {}

    public function index(): JsonResponse
    {
        $result = $this->roleService->getAllRoles();
        return $result->toResponse();
    }

    public function assign(AssignRoleRequest $request): JsonResponse
    {
        $dto = RoleAssignmentDto::fromArray($request->validated());
        $result = $this->roleService->assignRole($dto);
        return $result->toResponse();
    }

    public function remove(RemoveRoleRequest $request): JsonResponse
    {
        $dto = RoleAssignmentDto::fromArray($request->validated());
        $result = $this->roleService->removeRole($dto);
        return $result->toResponse();
    }

    public function userRoles(int $userId): JsonResponse
    {
        $result = $this->roleService->getUserRoles($userId);
        return $result->toResponse();
    }

    public function surveyRoles(int $surveyId): JsonResponse
    {
        $result = $this->roleService->getSurveyRoles($surveyId);
        return $result->toResponse();
    }

    public function userHasRole(int $userId, string $roleName): JsonResponse
    {
        $result = $this->roleService->userHasRole($userId, $roleName);
        return $result->toResponse();
    }

    public function surveyHasRole(int $surveyId, string $roleName): JsonResponse
    {
        $result = $this->roleService->surveyHasRole($surveyId, $roleName);
        return $result->toResponse();
    }
} 