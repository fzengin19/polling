<?php

namespace App\Repositories\Eloquent;

use App\Models\TemplateVersion;
use App\Repositories\Abstract\TemplateVersionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TemplateVersionRepository implements TemplateVersionRepositoryInterface
{
    public function __construct(
        protected TemplateVersion $model
    ) {}

    public function find(int $id): ?TemplateVersion
    {
        return $this->model->find($id);
    }

    public function findByTemplate(int $templateId): Collection
    {
        return $this->model->where('template_id', $templateId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLatestVersion(int $templateId): ?TemplateVersion
    {
        return $this->model->where('template_id', $templateId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function create(array $data): TemplateVersion
    {
        return $this->model->create($data);
    }

    public function delete(TemplateVersion $version): bool
    {
        return $version->delete();
    }
} 