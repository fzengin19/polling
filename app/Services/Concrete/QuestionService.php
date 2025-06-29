<?php

namespace App\Services\Concrete;

use App\Dtos\QuestionDto;
use App\Models\Question;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use App\Responses\Abstract\ResourceMapInterface;
use App\Services\Abstract\QuestionServiceInterface;
use Illuminate\Support\Collection;
use App\Responses\ServiceResponse;

class QuestionService implements QuestionServiceInterface
{
    public function __construct(
        protected QuestionRepositoryInterface $questionRepository,
        protected ResourceMapInterface $resourceMap
    ) {
    }
    
    public function findQuestion(int $id): ServiceResponse
    {
        $question = $this->questionRepository->find($id);
        $status = $question ? 200 : 404;
        return new ServiceResponse($question, $this->resourceMap, $status);
    }

    public function getQuestionsByPageId(int $pageId): ServiceResponse
    {
        $questions = $this->questionRepository->findBy('page_id', $pageId);
        return new ServiceResponse($questions, $this->resourceMap, 200);
    }

    public function createQuestion(QuestionDto $dto): ServiceResponse
    {
        $question = $this->questionRepository->create($dto->toDatabaseArray());
        return new ServiceResponse($question, $this->resourceMap, 201);
    }

    public function updateQuestion(int $id, QuestionDto $dto): ServiceResponse
    {
        $question = $this->questionRepository->find($id);
        if (!$question) {
            return new ServiceResponse(null, $this->resourceMap, 404);
        }

        $data = array_merge($dto->toDatabaseArray(), ['type' => $question->type]);
        $this->questionRepository->update($id, $data);
        
        return new ServiceResponse($this->questionRepository->find($id), $this->resourceMap, 200);
    }
    
    public function deleteQuestionById(int $id): ServiceResponse
    {
        $question = $this->questionRepository->find($id);
        if (!$question) {
            return new ServiceResponse(null, $this->resourceMap, 404);
        }

        $this->questionRepository->delete($id);
        return new ServiceResponse(null, $this->resourceMap, 204);
    }

    public function reorder(int $pageId, array $questionIds): ServiceResponse
    {
        foreach ($questionIds as $index => $questionId) {
            $this->questionRepository->update($questionId, ['order_index' => $index]);
        }
        return new ServiceResponse(['message' => 'Questions reordered successfully.'], $this->resourceMap, 200);
    }
} 