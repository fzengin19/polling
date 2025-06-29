<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\SurveyPage;
use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SurveyPageRepository extends BaseRepository implements SurveyPageRepositoryInterface
{
    public function __construct(SurveyPage $model)
    {
        parent::__construct($model);
    }

    public function findBySurvey(int $surveyId): Collection
    {
        return $this->model->where('survey_id', $surveyId)->get();
    }

    public function getOrderedPages(int $surveyId): Collection
    {
        return $this->model->where('survey_id', $surveyId)->orderBy('order_index')->get();
    }

    public function getNextOrderIndex(int $surveyId): int
    {
        return $this->model->where('survey_id', $surveyId)->max('order_index') + 1;
    }

    public function reorder(array $pageIds): void
    {
        if (empty($pageIds)) {
            return;
        }

        DB::transaction(function () use ($pageIds) {
            $baseOrder = $this->model->max('order_index') + 1;
            
            foreach ($pageIds as $index => $pageId) {
                $this->model->where('id', $pageId)->update(['order_index' => $baseOrder + $index]);
            }

            foreach ($pageIds as $index => $pageId) {
                $this->model->where('id', $pageId)->update(['order_index' => $index]);
            }
        });
    }
} 