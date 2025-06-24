<?php

namespace App\Services\Abstract;

use App\Dtos\RoleAssignmentDto;
use App\Responses\ServiceResponse;

interface RoleServiceInterface
{
    /**
     * Assign a role to a user or survey
     */
    public function assignRole(RoleAssignmentDto $dto): ServiceResponse;

    /**
     * Remove a role from a user or survey
     */
    public function removeRole(RoleAssignmentDto $dto): ServiceResponse;

    /**
     * Get all roles
     */
    public function getAllRoles(): ServiceResponse;

    /**
     * Get roles for a specific user
     */
    public function getUserRoles(int $userId): ServiceResponse;

    /**
     * Get roles for a specific survey
     */
    public function getSurveyRoles(int $surveyId): ServiceResponse;

    /**
     * Check if user has role
     */
    public function userHasRole(int $userId, string $roleName): ServiceResponse;

    /**
     * Check if survey has role
     */
    public function surveyHasRole(int $surveyId, string $roleName): ServiceResponse;
} 