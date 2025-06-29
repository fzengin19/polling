<?php

namespace App\Services\Concrete;

use App\Dtos\RoleAssignmentDto;
use App\Repositories\Abstract\RoleRepositoryInterface;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Repositories\Abstract\UserRepositoryInterface;
use App\Responses\ServiceResponse;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\RoleServiceInterface;
use Spatie\Permission\Models\Role;

class RoleService implements RoleServiceInterface
{
    public function __construct(
        protected ApiResourceMap $resourceMap,
        protected RoleRepositoryInterface $roleRepository,
        protected UserRepositoryInterface $userRepository,
        protected SurveyRepositoryInterface $surveyRepository
    ) {
    }

    public function assignRole(RoleAssignmentDto $dto): ServiceResponse
    {
        $role = $this->roleRepository->findByName($dto->roleName);
        if (!$role) {
            return new ServiceResponse(['error' => 'Role not found'], $this->resourceMap, 404);
        }

        if ($dto->userId) {
            $user = $this->userRepository->find($dto->userId);
            if (!$user) {
                return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
            }
            $user->assignRole($role);
        }

        if ($dto->surveyId) {
            $survey = $this->surveyRepository->find($dto->surveyId);
            if (!$survey) {
                return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
            }
            $survey->assignRole($role);
        }

        return new ServiceResponse(['message' => 'Role assigned successfully'], $this->resourceMap, 200);
    }

    public function removeRole(RoleAssignmentDto $dto): ServiceResponse
    {
        $role = $this->roleRepository->findByName($dto->roleName);
        if (!$role) {
            return new ServiceResponse(['error' => 'Role not found'], $this->resourceMap, 404);
        }

        if ($dto->userId) {
            $user = $this->userRepository->find($dto->userId);
            if (!$user) {
                return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
            }
            $user->removeRole($role);
        }

        if ($dto->surveyId) {
            $survey = $this->surveyRepository->find($dto->surveyId);
            if (!$survey) {
                return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
            }
            $survey->removeRole($role);
        }

        return new ServiceResponse(['message' => 'Role removed successfully'], $this->resourceMap, 200);
    }

    public function getAllRoles(): ServiceResponse
    {
        $roles = $this->roleRepository->all();
        return new ServiceResponse(['roles' => $roles], $this->resourceMap, 200);
    }

    public function getUserRoles(int $userId): ServiceResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
        }

        $roles = $user->roles;
        return new ServiceResponse(['roles' => $roles], $this->resourceMap, 200);
    }

    public function getSurveyRoles(int $surveyId): ServiceResponse
    {
        $survey = $this->surveyRepository->find($surveyId);
        if (!$survey) {
            return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
        }

        $roles = $survey->roles;
        return new ServiceResponse(['roles' => $roles], $this->resourceMap, 200);
    }

    public function userHasRole(int $userId, string $roleName): ServiceResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return new ServiceResponse(['error' => 'User not found'], $this->resourceMap, 404);
        }

        $hasRole = $user->hasRole($roleName);
        return new ServiceResponse(['has_role' => $hasRole], $this->resourceMap, 200);
    }

    public function surveyHasRole(int $surveyId, string $roleName): ServiceResponse
    {
        $survey = $this->surveyRepository->find($surveyId);
        if (!$survey) {
            return new ServiceResponse(['error' => 'Survey not found'], $this->resourceMap, 404);
        }

        $hasRole = $survey->hasRole($roleName);
        return new ServiceResponse(['has_role' => $hasRole], $this->resourceMap, 200);
    }
} 