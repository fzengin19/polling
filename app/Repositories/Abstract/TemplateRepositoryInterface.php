<?php

namespace App\Repositories\Abstract;

use App\Models\Template;
use Illuminate\Database\Eloquent\Collection;

interface TemplateRepositoryInterface
{
    /**
     * Find template by ID
     */
    public function find(int $id): ?Template;

    /**
     * Get all templates
     */
    public function all(): Collection;

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
    public function findByUserAndPublic(int $userId, bool $isPublic = null): Collection;

    /**
     * Create new template
     */
    public function create(array $data): Template;

    /**
     * Update template
     */
    public function update(Template $template, array $data): bool;

    /**
     * Delete template
     */
    public function delete(Template $template): bool;

    /**
     * Get templates forked from a specific template
     */
    public function getForks(int $templateId): Collection;
} 