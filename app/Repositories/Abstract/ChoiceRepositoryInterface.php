<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Dtos\ChoiceDto;

interface ChoiceRepositoryInterface extends BaseRepositoryInterface
{
    public function findByQuestion(int $questionId): array;
    public function reorder(int $questionId, array $choiceIds): bool;
} 