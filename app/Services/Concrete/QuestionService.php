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
        if (!$question) {
            return ServiceResponse::notFound('Question not found.');
        }
        $question->load('choices');
        return ServiceResponse::success($question);
    }

    public function getQuestionsByPageId(int $pageId): ServiceResponse
    {
        $questions = $this->questionRepository->findBy('page_id', $pageId);
        $questions->load('choices');
        return ServiceResponse::success($questions);
    }

    public function createQuestion(QuestionDto $dto): ServiceResponse
    {
        $question = $this->questionRepository->create($dto->toDatabaseArray());
        return ServiceResponse::created($question);
    }

    public function updateQuestion(int $id, QuestionDto $dto): ServiceResponse
    {
        $question = $this->questionRepository->find($id);
        if (!$question) {
            return ServiceResponse::notFound('Question not found.');
        }

        $data = array_merge($dto->toDatabaseArray(), ['type' => $question->type]);
        $this->questionRepository->update($id, $data);
        
        return ServiceResponse::success($this->questionRepository->find($id));
    }
    
    public function deleteQuestionById(int $id): ServiceResponse
    {
        $question = $this->questionRepository->find($id);
        if (!$question) {
            return ServiceResponse::notFound('Question not found.');
        }
        $this->questionRepository->delete($id);
        return ServiceResponse::noContent('Question deleted successfully.');
    }

    public function reorder(int $pageId, array $questionIds): ServiceResponse
    {
        $this->questionRepository->reorder($pageId, $questionIds);
        return ServiceResponse::success(null, 'Questions reordered successfully.');
    }
} 