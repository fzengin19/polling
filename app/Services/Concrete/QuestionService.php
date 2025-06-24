<?php

namespace App\Services\Concrete;

use App\Dtos\QuestionDto;
use App\Models\Question;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use App\Services\Abstract\QuestionServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class QuestionService implements QuestionServiceInterface
{
    public function __construct(protected QuestionRepositoryInterface $questions) {}

    public function getBySurveyPage(int $surveyPageId): Collection
    {
        return $this->questions->getBySurveyPage($surveyPageId);
    }

    public function getActiveBySurveyPage(int $surveyPageId): Collection
    {
        return $this->questions->getActiveBySurveyPage($surveyPageId);
    }

    public function findById(int $id): ?Question
    {
        return $this->questions->findById($id);
    }

    public function create(QuestionDto $dto): Question
    {
        return $this->questions->create($dto->toArray());
    }

    public function update(Question $question, QuestionDto $dto): Question
    {
        return $this->questions->update($question, $dto->toArray());
    }

    public function delete(Question $question): bool
    {
        return $this->questions->delete($question);
    }

    public function reorder(int $surveyPageId, array $questionIds): bool
    {
        return $this->questions->reorder($surveyPageId, $questionIds);
    }

    public function getByType(int $surveyPageId, string $type): Collection
    {
        return $this->questions->getByType($surveyPageId, $type);
    }
} 