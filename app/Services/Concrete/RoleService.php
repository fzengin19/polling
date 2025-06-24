<?php

namespace App\Services\Concrete;

use App\Dtos\RoleAssignmentDto;
use App\Models\User;
use App\Models\Survey;
use App\Responses\ServiceResponse;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\RoleServiceInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleService implements RoleServiceInterface
{
    public function __construct(
        protected ApiResourceMap $resourceMap
    ) {}

    public function assignRole(RoleAssignmentDto $dto): ServiceResponse
    {
        try {
            // Check if role exists
            $role = Role::where('name', $dto->roleName)->first();
            if (!$role) {
                return new ServiceResponse(['error' => 'Role not found'], $this->resourceMap, 404);
            }

            if ($dto->userId) {
                $user = User::find($dto->userId);
                if (!$user) {
                    return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
                }
                $user->assignRole($dto->roleName);
            }

            if ($dto->surveyId) {
                $survey = Survey::find($dto->surveyId);
                if (!$survey) {
                    return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
                }
                $survey->assignRole($dto->roleName);
            }

            return new ServiceResponse(['message' => 'Role assigned successfully'], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function removeRole(RoleAssignmentDto $dto): ServiceResponse
    {
        try {
            // Check if role exists
            $role = Role::where('name', $dto->roleName)->first();
            if (!$role) {
                return new ServiceResponse(['error' => 'Role not found'], $this->resourceMap, 404);
            }

            if ($dto->userId) {
                $user = User::find($dto->userId);
                if (!$user) {
                    return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
                }
                $user->removeRole($dto->roleName);
            }

            if ($dto->surveyId) {
                $survey = Survey::find($dto->surveyId);
                if (!$survey) {
                    return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
                }
                $survey->removeRole($dto->roleName);
            }

            return new ServiceResponse(['message' => 'Role removed successfully'], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getAllRoles(): ServiceResponse
    {
        try {
            $roles = Role::with('permissions')->get();
            return new ServiceResponse(['roles' => $roles], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getUserRoles(int $userId): ServiceResponse
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
            }

            $roles = $user->roles;
            return new ServiceResponse(['roles' => $roles], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getSurveyRoles(int $surveyId): ServiceResponse
    {
        try {
            $survey = Survey::find($surveyId);
            if (!$survey) {
                return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
            }

            $roles = $survey->roles;
            return new ServiceResponse(['roles' => $roles], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function userHasRole(int $userId, string $roleName): ServiceResponse
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
            }

            $hasRole = $user->hasRole($roleName);
            return new ServiceResponse(['has_role' => $hasRole], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function surveyHasRole(int $surveyId, string $roleName): ServiceResponse
    {
        try {
            $survey = Survey::find($surveyId);
            if (!$survey) {
                return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
            }

            $hasRole = $survey->hasRole($roleName);
            return new ServiceResponse(['has_role' => $hasRole], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }
} 