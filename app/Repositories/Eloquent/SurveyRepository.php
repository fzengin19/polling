<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\Survey;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class SurveyRepository extends BaseRepository implements SurveyRepositoryInterface
{
    public function __construct(Survey $model)
    {
        parent::__construct($model);
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

    public function getExpiredSurveys(): Collection
    {
        return $this->model->where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return $this->model->where('created_by', $userId)->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getByTemplate(int $templateId): Collection
    {
        return $this->model->where('template_id', $templateId)->get();
    }

    public function duplicate(int $id): int
    {
        $original = $this->find($id);
        if (!$original) {
            throw new \Exception('Original survey not found for duplication.');
        }

        $clone = $original->replicate();
        $clone->title = $original->title . ' (Copy)';
        $clone->status = 'draft';
        $clone->created_at = now();
        $clone->updated_at = now();
        $clone->save();

        return $clone->id;
    }
} 