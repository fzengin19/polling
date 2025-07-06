<?php

namespace App\Http\Controllers\Api;

use App\Services\Abstract\RoleServiceInterface;
use App\Http\Requests\Role\AssignRoleRequest;
use App\Http\Requests\Role\RemoveRoleRequest;
use App\Dtos\RoleAssignmentDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @group Role Management
 *
 * APIs for managing user and survey roles
 */
class RoleController extends Controller
{
    public function __construct(
        protected RoleServiceInterface $roleService
    ) {}

    /**
     * List Roles
     *
     * Get a list of all available roles in the system.
     * @authenticated
     * @response 200 {"success": true, "data": [{"name": "admin", "description": "Administrator role"}]}
     */
    public function index(): JsonResponse
    {
        $result = $this->roleService->getAllRoles();
        return $result->toResponse();
    }

    /**
     * Assign Role
     *
     * Assign a role to a user or survey.
     * @authenticated
     * @response 200 {"success": true, "message": "Role assigned successfully", "data": null}
     */
    public function assign(AssignRoleRequest $request): JsonResponse
    {
        $dto = RoleAssignmentDto::fromArray($request->validated());
        $result = $this->roleService->assignRole($dto);
        return $result->toResponse();
    }

    /**
     * Remove Role
     *
     * Remove a role from a user or survey.
     * @authenticated
     * @response 200 {"success": true, "message": "Role removed successfully", "data": null}
     */
    public function remove(RemoveRoleRequest $request): JsonResponse
    {
        $dto = RoleAssignmentDto::fromArray($request->validated());
        $result = $this->roleService->removeRole($dto);
        return $result->toResponse();
    }

    /**
     * Get User Roles
     *
     * Get all roles assigned to a specific user.
     * @authenticated
     * @urlParam userId required The ID of the user. Example: 1
     * @response 200 {"success": true, "data": [{"name": "editor", "model_type": "user", "model_id": 1}]}
     */
    public function userRoles(int $userId): JsonResponse
    {
        $result = $this->roleService->getUserRoles($userId);
        return $result->toResponse();
    }

    /**
     * Get Survey Roles
     *
     * Get all roles assigned to a specific survey.
     * @authenticated
     * @urlParam surveyId required The ID of the survey. Example: 1
     * @response 200 {"success": true, "data": [{"name": "viewer", "model_type": "survey", "model_id": 1}]}
     */
    public function surveyRoles(int $surveyId): JsonResponse
    {
        $result = $this->roleService->getSurveyRoles($surveyId);
        return $result->toResponse();
    }

    /**
     * Check User Role
     *
     * Check if a user has a specific role.
     * @authenticated
     * @urlParam userId required The ID of the user. Example: 1
     * @urlParam roleName required The name of the role. Example: admin
     * @response 200 {"success": true, "data": {"has_role": true}}
     */
    public function userHasRole(int $userId, string $roleName): JsonResponse
    {
        $result = $this->roleService->userHasRole($userId, $roleName);
        return $result->toResponse();
    }

    /**
     * Check Survey Role
     *
     * Check if a survey has a specific role assigned.
     * @authenticated
     * @urlParam surveyId required The ID of the survey. Example: 1
     * @urlParam roleName required The name of the role. Example: public
     * @response 200 {"success": true, "data": {"has_role": false}}
     */
    public function surveyHasRole(int $surveyId, string $roleName): JsonResponse
    {
        $result = $this->roleService->surveyHasRole($surveyId, $roleName);
        return $result->toResponse();
    }
} 