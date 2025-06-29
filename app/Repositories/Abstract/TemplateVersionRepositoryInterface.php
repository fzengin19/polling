<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Models\TemplateVersion;
use Illuminate\Database\Eloquent\Collection;

interface TemplateVersionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all versions for a template
     */
    public function findByTemplate(int $templateId): Collection;

    /**
     * Get latest version for a template
     */
    public function getLatestVersion(int $templateId): ?TemplateVersion;
} 