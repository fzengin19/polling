<?php

namespace App\Services\Concrete;

use App\Dtos\ChoiceDto;
use App\Models\Choice;
use App\Repositories\Abstract\ChoiceRepositoryInterface;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use App\Responses\Abstract\ResourceMapInterface;
use App\Responses\ServiceResponse;
use App\Services\Abstract\ChoiceServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChoiceService implements ChoiceServiceInterface
{
    public function __construct(
        protected ChoiceRepositoryInterface $choiceRepository,
        protected QuestionRepositoryInterface $questionRepository,
        protected ResourceMapInterface $resourceMap
    ) {
    }

    public function getByQuestion(int $questionId): ServiceResponse
    {
        $choices = $this->choiceRepository->findByQuestion($questionId);
        return new ServiceResponse($choices, $this->resourceMap);
    }

    public function createChoice(ChoiceDto $dto): ServiceResponse
    {
        $question = $this->questionRepository->find($dto->question_id);
        if (!$question) {
            return new ServiceResponse(['message' => 'Question not found.'], $this->resourceMap, 404);
        }

        if ($question->type !== 'multiple_choice') {
            return new ServiceResponse(['message' => 'Choices can only be added to multiple_choice questions.'], $this->resourceMap, 422);
        }
        
        $data = $dto->toDatabaseArray();
        $data['order_index'] = $this->choiceRepository->getNextOrderIndex($dto->question_id);

        $choice = $this->choiceRepository->create($data);
        return new ServiceResponse($choice, $this->resourceMap, 201);
    }

    public function updateChoice(int $id, ChoiceDto $dto): ServiceResponse
    {
        $choice = $this->choiceRepository->find($id);
        if (!$choice) {
            return new ServiceResponse(null, $this->resourceMap, 404);
        }

        $this->choiceRepository->update($id, $dto->toDatabaseArray());
        $updatedChoice = $this->choiceRepository->find($id);
        return new ServiceResponse($updatedChoice, $this->resourceMap);
    }

    public function deleteChoiceById(int $id): ServiceResponse
    {
        $choice = $this->choiceRepository->find($id);
        if (!$choice) {
            return new ServiceResponse(null, $this->resourceMap, 404);
        }
        $this->choiceRepository->delete($id);
        return new ServiceResponse(null, $this->resourceMap, 204);
    }

    public function reorder(int $questionId, array $choiceIds): ServiceResponse
    {
        try {
            DB::transaction(function () use ($choiceIds) {
                foreach ($choiceIds as $index => $choiceId) {
                    $this->choiceRepository->update($choiceId, ['order_index' => $index]);
                }
            });
            return new ServiceResponse(['message' => 'Choices reordered successfully.'], $this->resourceMap);
        } catch (\Exception $e) {
            return new ServiceResponse(['message' => 'An error occurred while reordering choices.'], $this->resourceMap, 500);
        }
    }

    public function findChoice(int $id): ServiceResponse
    {
        $choice = $this->choiceRepository->find($id);
        $status = $choice ? 200 : 404;
        return new ServiceResponse($choice, $this->resourceMap, $status);
    }
} 