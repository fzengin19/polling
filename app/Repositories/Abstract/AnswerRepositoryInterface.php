<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Dtos\AnswerDto;

interface AnswerRepositoryInterface extends BaseRepositoryInterface
{
    public function findByResponse(int $responseId): array;
    public function findByQuestion(int $questionId): array;
    public function createMany(array $answers): array;
} 