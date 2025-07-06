<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\Response;
use App\Repositories\Abstract\ResponseRepositoryInterface;

class ResponseRepository extends BaseRepository implements ResponseRepositoryInterface
{
    public function __construct(Response $model)
    {
        parent::__construct($model);
    }

    public function getCountBySurvey(int $surveyId): int
    {
        return $this->model->where('survey_id', $surveyId)->count();
    }

    public function getCompletedCountBySurvey(int $surveyId): int
    {
        return $this->model->where('survey_id', $surveyId)->where('is_complete', true)->count();
    }

    public function getStatistics(int $surveyId): array
    {
        $totalResponses = $this->model->where('survey_id', $surveyId)->count();
        $completedResponses = $this->model->where('survey_id', $surveyId)->where('is_complete', true)->count();

        return [
            'total_responses' => $totalResponses,
            'completed_responses' => $completedResponses,
            'incomplete_responses' => $totalResponses - $completedResponses,
        ];
    }

    public function countBySurvey(int $surveyId): int
    {
        return $this->model->where('survey_id', $surveyId)->where('is_complete', true)->count();
    }
} 