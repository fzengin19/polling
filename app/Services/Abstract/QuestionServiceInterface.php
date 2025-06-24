<?php

namespace App\Services\Abstract;

use App\Dtos\QuestionDto;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;

interface QuestionServiceInterface
{
    public function getBySurveyPage(int $surveyPageId): Collection;
    public function getActiveBySurveyPage(int $surveyPageId): Collection;
    public function findById(int $id): ?Question;
    public function create(QuestionDto $dto): Question;
    public function update(Question $question, QuestionDto $dto): Question;
    public function delete(Question $question): bool;
    public function reorder(int $surveyPageId, array $questionIds): bool;
    public function getByType(int $surveyPageId, string $type): Collection;
} 