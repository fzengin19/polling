<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\Choice;
use App\Repositories\Abstract\ChoiceRepositoryInterface;
use Illuminate\Support\Collection;

class ChoiceRepository extends BaseRepository implements ChoiceRepositoryInterface
{
    public function __construct(Choice $model)
    {
        parent::__construct($model);
    }

    public function findByQuestion(int $questionId): Collection
    {
        return $this->model->where('question_id', $questionId)
            ->orderBy('order_index')
            ->get();
    }

    public function reorder(int $questionId, array $choicesData): void
    {
        foreach ($choicesData as $choiceData) {
            $this->model->where('id', $choiceData['id'])
                ->where('question_id', $questionId)
                ->update(['order_index' => $choiceData['order_index']]);
        }
    }

    public function getNextOrderIndex(int $questionId): int
    {
        return $this->model->where('question_id', $questionId)->max('order_index') + 1;
    }
} 