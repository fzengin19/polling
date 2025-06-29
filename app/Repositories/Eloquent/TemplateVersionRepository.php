<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\TemplateVersion;
use App\Repositories\Abstract\TemplateVersionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TemplateVersionRepository extends BaseRepository implements TemplateVersionRepositoryInterface
{
    public function __construct(TemplateVersion $model)
    {
        parent::__construct($model);
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
} 