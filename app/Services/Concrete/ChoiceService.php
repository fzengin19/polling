<?php

namespace App\Services\Concrete;

use App\Dtos\ChoiceDto;
use App\Models\Choice;
use App\Models\Question;
use App\Responses\ServiceResponse;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\ChoiceServiceInterface;
use Illuminate\Support\Facades\DB;

class ChoiceService implements ChoiceServiceInterface
{
    public function __construct(
        protected ApiResourceMap $resourceMap
    ) {}

    public function create(ChoiceDto $dto): ServiceResponse
    {
        try {
            DB::beginTransaction();

            // Check if question exists
            $question = Question::find($dto->question_id);
            if (!$question) {
                return new ServiceResponse(['error' => 'Question not found'], $this->resourceMap, 404);
            }

            $choice = Choice::create($dto->toArray());
            
            DB::commit();
            return new ServiceResponse(['choice' => $choice], $this->resourceMap, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function update(int $id, ChoiceDto $dto): ServiceResponse
    {
        try {
            $choice = Choice::find($id);
            if (!$choice) {
                return new ServiceResponse(['error' => 'Choice not found'], $this->resourceMap, 404);
            }

            // Check if question exists if question_id is being updated
            if ($dto->question_id !== $choice->question_id) {
                $question = Question::find($dto->question_id);
                if (!$question) {
                    return new ServiceResponse(['error' => 'Question not found'], $this->resourceMap, 404);
                }
            }

            $choice->update($dto->toArray());
            return new ServiceResponse(['choice' => $choice], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function delete(int $id): ServiceResponse
    {
        try {
            $choice = Choice::find($id);
            if (!$choice) {
                return new ServiceResponse(['error' => 'Choice not found'], $this->resourceMap, 404);
            }

            $choice->delete();
            return new ServiceResponse(['message' => 'Choice deleted successfully'], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getByQuestion(int $questionId): ServiceResponse
    {
        try {
            // Check if question exists
            $question = Question::find($questionId);
            if (!$question) {
                return new ServiceResponse(['error' => 'Question not found'], $this->resourceMap, 404);
            }

            $choices = $question->choices()->orderBy('order_index')->get();
            return new ServiceResponse(['choices' => $choices], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function reorder(int $questionId, array $choiceIds): ServiceResponse
    {
        try {
            // Check if question exists
            $question = Question::find($questionId);
            if (!$question) {
                return new ServiceResponse(['error' => 'Question not found'], $this->resourceMap, 404);
            }

            foreach ($choiceIds as $index => $choiceId) {
                $choice = Choice::where('id', $choiceId)
                    ->where('question_id', $questionId)
                    ->first();
                
                if ($choice) {
                    $choice->update(['order_index' => $index]);
                }
            }

            return new ServiceResponse(['message' => 'Choices reordered successfully'], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }
} 