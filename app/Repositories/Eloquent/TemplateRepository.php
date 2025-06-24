<?php

namespace App\Repositories\Eloquent;

use App\Models\Template;
use App\Repositories\Abstract\TemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TemplateRepository implements TemplateRepositoryInterface
{
    public function __construct(
        protected Template $model
    ) {}

    public function find(int $id): ?Template
    {
        return $this->model->find($id);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model->where('created_by', $userId)->get();
    }

    public function getPublicTemplates(): Collection
    {
        return $this->model->where('is_public', true)->get();
    }

    public function findByUserAndPublic(int $userId, bool $isPublic = null): Collection
    {
        $query = $this->model->where('created_by', $userId);
        
        if ($isPublic !== null) {
            $query->where('is_public', $isPublic);
        }
        
        return $query->get();
    }

    public function create(array $data): Template
    {
        return $this->model->create($data);
    }

    public function update(Template $template, array $data): bool
    {
        return $template->update($data);
    }

    public function delete(Template $template): bool
    {
        return $template->delete();
    }

    public function getForks(int $templateId): Collection
    {
        return $this->model->where('forked_from_template_id', $templateId)->get();
    }
} 