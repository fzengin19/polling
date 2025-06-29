<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface SurveyRepositoryInterface extends BaseRepositoryInterface
{
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
     * Get surveys that need to be archived (expired)
     */
    public function getExpiredSurveys(): Collection;
} 