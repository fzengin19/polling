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
        return ServiceResponse::success($choices);
    }

    public function createChoice(ChoiceDto $dto): ServiceResponse
    {
        $question = $this->questionRepository->find($dto->question_id);
        if (!$question) {
            return ServiceResponse::notFound('Question not found.');
        }

        if ($question->type !== 'multiple_choice') {
            return ServiceResponse::error('Choices can only be added to multiple_choice questions.', null, 422);
        }
        
        $data = $dto->toDatabaseArray();
        $data['order_index'] = $this->choiceRepository->getNextOrderIndex($dto->question_id);

        $choice = $this->choiceRepository->create($data);
        return ServiceResponse::created($choice);
    }

    public function updateChoice(int $id, ChoiceDto $dto): ServiceResponse
    {
        $choice = $this->choiceRepository->find($id);
        if (!$choice) {
            return ServiceResponse::notFound('Choice not found.');
        }

        $this->choiceRepository->update($id, $dto->toDatabaseArray());
        $updatedChoice = $this->choiceRepository->find($id);
        return ServiceResponse::success($updatedChoice);
    }

    public function deleteChoiceById(int $id): ServiceResponse
    {
        $choice = $this->choiceRepository->find($id);
        if (!$choice) {
            return ServiceResponse::notFound('Choice not found.');
        }
        $this->choiceRepository->delete($id);
        return ServiceResponse::noContent('Choice deleted successfully.');
    }

    public function reorder(int $questionId, array $choiceIds): ServiceResponse
    {
        try {
            DB::transaction(function () use ($choiceIds) {
                foreach ($choiceIds as $index => $choiceId) {
                    $this->choiceRepository->update($choiceId, ['order_index' => $index]);
                }
            });
            return ServiceResponse::success(null, 'Choices reordered successfully.');
        } catch (\Exception $e) {
            return ServiceResponse::error('An error occurred while reordering choices.', null, 500);
        }
    }

    public function findChoice(int $id): ServiceResponse
    {
        $choice = $this->choiceRepository->find($id);
        if (!$choice) {
            return ServiceResponse::notFound('Choice not found.');
        }
        return ServiceResponse::success($choice);
    }
} 