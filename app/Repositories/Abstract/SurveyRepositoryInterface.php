<?php

namespace App\Repositories\Abstract;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Collection;

interface SurveyRepositoryInterface
{
    /**
     * Find survey by ID
     */
    public function find(int $id): ?Survey;

    /**
     * Get all surveys
     */
    public function all(): Collection;

    /**
     * Get surveys by user ID
     */
    public function findByUser(int $userId): Collection;

    /**
     * Get surveys by status
     */
    public function findByStatus(string $status): Collection;

    /**
     * Get active surveys
     */
    public function getActiveSurveys(): Collection;

    /**
     * Get surveys by user ID and status
     */
    public function findByUserAndStatus(int $userId, string $status): Collection;

    /**
     * Get surveys based on template
     */
    public function findByTemplate(int $templateId): Collection;

    /**
     * Create new survey
     */
    public function create(array $data): Survey;

    /**
     * Update survey
     */
    public function update(Survey $survey, array $data): bool;

    /**
     * Delete survey
     */
    public function delete(Survey $survey): bool;

    /**
     * Get surveys that need to be archived (expired)
     */
    public function getExpiredSurveys(): Collection;
} 