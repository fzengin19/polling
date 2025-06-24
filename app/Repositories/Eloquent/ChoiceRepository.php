<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Dtos\ChoiceDto;
use App\Models\Choice;
use App\Repositories\Abstract\ChoiceRepositoryInterface;

class ChoiceRepository extends BaseRepository implements ChoiceRepositoryInterface
{
    public function __construct(Choice $model)
    {
        parent::__construct($model);
    }

    public function findByQuestion(int $questionId): array
    {
        $choices = $this->model->where('question_id', $questionId)
            ->orderBy('order_index')
            ->get();

        return $choices->map(fn($choice) => ChoiceDto::fromArray($choice->toArray()))->toArray();
    }

    public function reorder(int $questionId, array $choiceIds): bool
    {
        foreach ($choiceIds as $index => $choiceId) {
            $this->model->where('id', $choiceId)
                ->where('question_id', $questionId)
                ->update(['order_index' => $index]);
        }

        return true;
    }

    protected function dtoToArray($dto): array
    {
        return $dto->toArray();
    }

    protected function modelToDto($model): ChoiceDto
    {
        return ChoiceDto::fromArray($model->toArray());
    }
} 