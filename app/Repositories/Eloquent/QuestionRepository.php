<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\Question;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class QuestionRepository extends BaseRepository implements QuestionRepositoryInterface
{
    public function __construct(Question $model)
    {
        parent::__construct($model);
    }

    public function getBySurveyPage(int $surveyPageId): Collection
    {
        return $this->model->where('page_id', $surveyPageId)->orderBy('order_index')->get();
    }

    public function getActiveBySurveyPage(int $surveyPageId): Collection
    {
        return $this->model->where('page_id', $surveyPageId)
            ->orderBy('order_index')
            ->get();
    }

    public function reorder(int $surveyPageId, array $questionIds): bool
    {
        foreach ($questionIds as $order => $id) {
            $this->model->where('id', $id)
                ->where('page_id', $surveyPageId)
                ->update(['order_index' => $order]);
        }
        return true;
    }

    public function getByType(int $surveyPageId, string $type): Collection
    {
        return $this->model->where('page_id', $surveyPageId)
            ->where('type', $type)
            ->orderBy('order_index')
            ->get();
    }

    public function getOrderedPages(int $pageId): Collection
    {
        return $this->model->where('page_id', $pageId)->orderBy('order_index', 'asc')->get();
    }

    public function findByJsonContains(string $column, string $key, $value): Collection
    {
        // Using a LIKE query for robust searching within the JSON array of objects.
        // Example: "media_id":1
        $searchTerm = "\"{$key}\":{$value}";
        return $this->model->where($column, 'LIKE', "%{$searchTerm}%")->get();
    }
} 