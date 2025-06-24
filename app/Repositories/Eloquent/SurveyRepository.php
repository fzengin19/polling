<?php

namespace App\Repositories\Eloquent;

use App\Models\Survey;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class SurveyRepository implements SurveyRepositoryInterface
{
    public function __construct(
        protected Survey $model
    ) {}

    public function find(int $id): ?Survey
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

    public function findByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getActiveSurveys(): Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    public function findByUserAndStatus(int $userId, string $status): Collection
    {
        return $this->model->where('created_by', $userId)
            ->where('status', $status)
            ->get();
    }

    public function findByTemplate(int $templateId): Collection
    {
        return $this->model->where('template_id', $templateId)->get();
    }

    public function create(array $data): Survey
    {
        return $this->model->create($data);
    }

    public function update(Survey $survey, array $data): bool
    {
        return $survey->update($data);
    }

    public function delete(Survey $survey): bool
    {
        return $survey->delete();
    }

    public function getExpiredSurveys(): Collection
    {
        return $this->model->where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->get();
    }
} 