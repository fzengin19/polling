<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\Template;
use App\Repositories\Abstract\TemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TemplateRepository extends BaseRepository implements TemplateRepositoryInterface
{
    public function __construct(Template $model)
    {
        parent::__construct($model);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model->where('created_by', $userId)->get();
    }

    public function getPublicTemplates(): Collection
    {
        return $this->model->where('is_public', true)->get();
    }

    public function findByUserAndPublic(int $userId, ?bool $isPublic = null): Collection
    {
        $query = $this->model->where('created_by', $userId);
        if ($isPublic !== null) {
            $query->where('is_public', $isPublic);
        }
        return $query->get();
    }

    public function getForks(int $templateId): Collection
    {
        return $this->model->where('forked_from_template_id', $templateId)->get();
    }
} 