<?php

namespace App\Repositories\Abstract;

use App\Models\TemplateVersion;
use Illuminate\Database\Eloquent\Collection;

interface TemplateVersionRepositoryInterface
{
    /**
     * Find version by ID
     */
    public function find(int $id): ?TemplateVersion;

    /**
     * Get all versions for a template
     */
    public function findByTemplate(int $templateId): Collection;

    /**
     * Get latest version for a template
     */
    public function getLatestVersion(int $templateId): ?TemplateVersion;

    /**
     * Create new version
     */
    public function create(array $data): TemplateVersion;

    /**
     * Delete version
     */
    public function delete(TemplateVersion $version): bool;
} 