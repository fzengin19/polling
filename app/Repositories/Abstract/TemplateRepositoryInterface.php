<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface TemplateRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get templates by user ID
     */
    public function findByUser(int $userId): Collection;

    /**
     * Get public templates
     */
    public function getPublicTemplates(): Collection;

    /**
     * Get templates by user ID and public status
     */
    public function findByUserAndPublic(int $userId, ?bool $isPublic = null): Collection;

    /**
     * Get templates forked from a specific template
     */
    public function getForks(int $templateId): Collection;
} 