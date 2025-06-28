<?php

namespace App\Services\Concrete;

use App\Dtos\QuestionDto;
use App\Models\Question;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use App\Services\Abstract\QuestionServiceInterface;
use App\Responses\ServiceResponse;
use App\Responses\Concrete\ApiResourceMap;

class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        protected QuestionRepositoryInterface $questions,
        protected ApiResourceMap $resourceMap
    ) {}

    public function getBySurveyPage(int $surveyPageId): ServiceResponse
    {
        $questions = $this->questions->getBySurveyPage($surveyPageId);
        return new ServiceResponse($questions, $this->resourceMap, 200);
    }

    public function getActiveBySurveyPage(int $surveyPageId): ServiceResponse
    {
        $questions = $this->questions->getActiveBySurveyPage($surveyPageId);
        return new ServiceResponse($questions, $this->resourceMap, 200);
    }

    public function findById(int $id): ServiceResponse
    {
        $question = $this->questions->findById($id);
        if (!$question) {
            return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
        }
        return new ServiceResponse($question, $this->resourceMap, 200);
    }

    public function create(QuestionDto $dto): ServiceResponse
    {
        $question = $this->questions->create($dto->toArray());
        return new ServiceResponse($question, $this->resourceMap, 201);
    }

    public function update(int $id, QuestionDto $dto): ServiceResponse
    {
        $question = $this->questions->findById($id);
        if (!$question) {
            return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
        }
        $updated = $this->questions->update($question, $dto->toArray());
        return new ServiceResponse($updated, $this->resourceMap, 200);
    }

    public function delete(int $id): ServiceResponse
    {
        $question = $this->questions->findById($id);
        if (!$question) {
            return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
        }
        $this->questions->delete($question);
        return new ServiceResponse(['message' => 'Question deleted'], $this->resourceMap, 200);
    }

    public function reorder(int $surveyPageId, array $questionIds): ServiceResponse
    {
        $this->questions->reorder($surveyPageId, $questionIds);
        return new ServiceResponse(['message' => 'Questions reordered'], $this->resourceMap, 200);
    }

    public function getByType(int $surveyPageId, string $type): ServiceResponse
    {
        $questions = $this->questions->getByType($surveyPageId, $type);
        return new ServiceResponse($questions, $this->resourceMap, 200);
    }
} 