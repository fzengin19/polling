<?php

namespace App\Repositories\Eloquent;

use App\Models\SurveyPage;
use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SurveyPageRepository implements SurveyPageRepositoryInterface
{
    public function __construct(
        protected SurveyPage $model
    ) {}

    public function find(int $id): ?SurveyPage
    {
        return $this->model->find($id);
    }

    public function findBySurvey(int $surveyId): Collection
    {
        return $this->model->where('survey_id', $surveyId)->get();
    }

    public function getOrderedPages(int $surveyId): Collection
    {
        return $this->model->where('survey_id', $surveyId)
            ->orderBy('order_index', 'asc')
            ->get();
    }

    public function getNextOrderIndex(int $surveyId): int
    {
        $maxOrder = $this->model->where('survey_id', $surveyId)
            ->max('order_index');
        
        return ($maxOrder ?? -1) + 1;
    }

    public function create(array $data): SurveyPage
    {
        return $this->model->create($data);
    }

    public function update(SurveyPage $page, array $data): bool
    {
        return $page->update($data);
    }

    public function delete(SurveyPage $page): bool
    {
        return $page->delete();
    }

    public function reorder(int $surveyId, array $pageIds): bool
    {
        return DB::transaction(function () use ($surveyId, $pageIds) {
            // 1. Her sayfanın order_index'ini geçici büyük değere ata
            $pages = $this->model->whereIn('id', $pageIds)->get();
            foreach ($pages as $page) {
                $page->order_index = $page->order_index + 1000;
                $page->save();
            }
            // 2. Yeni sıralamaya göre gerçek order_index ata
            foreach ($pageIds as $index => $pageId) {
                $this->model->where('id', $pageId)
                    ->where('survey_id', $surveyId)
                    ->update(['order_index' => $index]);
            }
            return true;
        });
    }
} 