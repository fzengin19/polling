<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use Illuminate\Support\Collection;

interface ChoiceRepositoryInterface extends BaseRepositoryInterface
{
    public function findByQuestion(int $questionId): Collection;
    public function reorder(int $questionId, array $choicesData): void;
    public function getNextOrderIndex(int $questionId): int;
} 