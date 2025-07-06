<?php

namespace App\Services\Concrete;

use App\Dtos\RoleAssignmentDto;
use App\Repositories\Abstract\RoleRepositoryInterface;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Repositories\Abstract\UserRepositoryInterface;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use App\Services\Abstract\RoleServiceInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleService implements RoleServiceInterface
{
    public function __construct(
        protected ResourceMapInterface $resourceMap,
        protected RoleRepositoryInterface $roleRepository,
        protected UserRepositoryInterface $userRepository,
        protected SurveyRepositoryInterface $surveyRepository
    ) {
    }

    public function assignRole(RoleAssignmentDto $dto): ServiceResponse
    {
        $model = $this->getModel($dto->modelType, $dto->modelId);
        $this->roleRepository->assignRoleToModel($model, $dto->roleName);
        return ServiceResponse::success(null, 'Role assigned successfully.');
    }

    public function removeRole(RoleAssignmentDto $dto): ServiceResponse
    {
        $model = $this->getModel($dto->modelType, $dto->modelId);
        $this->roleRepository->removeRoleFromModel($model, $dto->roleName);
        return ServiceResponse::success(null, 'Role removed successfully.');
    }

    public function getAllRoles(): ServiceResponse
    {
        $roles = $this->roleRepository->all();
        return ServiceResponse::success(['roles' => $roles]);
    }

    public function getUserRoles(int $userId): ServiceResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return ServiceResponse::notFound('User not found.');
        }

        $roles = $user->roles;
        return ServiceResponse::success(['roles' => $roles]);
    }

    public function getSurveyRoles(int $surveyId): ServiceResponse
    {
        $survey = $this->surveyRepository->find($surveyId);
        if (!$survey) {
            return ServiceResponse::notFound('Survey not found.');
        }

        $roles = $survey->roles;
        return ServiceResponse::success(['roles' => $roles]);
    }

    public function userHasRole(int $userId, string $roleName): ServiceResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return ServiceResponse::notFound('User not found.');
        }

        $hasRole = $user->hasRole($roleName);
        return ServiceResponse::success(['has_role' => $hasRole]);
    }

    public function surveyHasRole(int $surveyId, string $roleName): ServiceResponse
    {
        $survey = $this->surveyRepository->find($surveyId);
        if (!$survey) {
            return ServiceResponse::notFound('Survey not found.');
        }

        $hasRole = $survey->hasRole($roleName);
        return ServiceResponse::success(['has_role' => $hasRole]);
    }

    private function getModel(string $modelType, int $modelId)
    {
        $repository = match ($modelType) {
            'user' => $this->userRepository,
            'survey' => $this->surveyRepository,
            default => throw new \InvalidArgumentException("Invalid model type: {$modelType}"),
        };

        $model = $repository->find($modelId);
        if (!$model) {
            throw new ModelNotFoundException(ucfirst($modelType) . ' not found.');
        }

        return $model;
    }
} 