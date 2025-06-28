<?php

namespace App\Services\Concrete;

use App\Dtos\ChoiceDto;
use App\Models\Choice;
use App\Models\Question;
use App\Models\SurveyPage;
use App\Models\Survey;
use App\Responses\ServiceResponse;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\ChoiceServiceInterface;
use Illuminate\Support\Facades\Auth;
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

            $question = Question::find($dto->question_id);
            if (!$question) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
            }
            
            // Check if question type is multiple_choice
            if ($question->type !== 'multiple_choice') {
                DB::rollBack();
                return new ServiceResponse(['errors' => ['question_id' => ['Choices can only be added to multiple_choice questions']]], $this->resourceMap, 422);
            }
            
            // Check authorization
            $surveyPage = SurveyPage::find($question->page_id);
            $survey = $surveyPage ? Survey::find($surveyPage->survey_id) : null;
            if (!$survey || $survey->created_by !== Auth::id()) {
                DB::rollBack();
                return new ServiceResponse(['message' => 'Forbidden'], $this->resourceMap, 403);
            }

            $choice = Choice::create($dto->toArray());
            DB::commit();
            return new ServiceResponse($choice, $this->resourceMap, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function update(int $id, ChoiceDto $dto): ServiceResponse
    {
        try {
            $choice = Choice::find($id);
            if (!$choice) {
                return new ServiceResponse(['message' => 'Choice not found'], $this->resourceMap, 404);
            }
            $question = Question::find($choice->question_id);
            if (!$question) {
                return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
            }
            $surveyPage = SurveyPage::find($question->page_id);
            $survey = $surveyPage ? Survey::find($surveyPage->survey_id) : null;
            if (!$survey || $survey->created_by !== Auth::id()) {
                return new ServiceResponse(['message' => 'Forbidden'], $this->resourceMap, 403);
            }
            $choice->update([
                'label' => $dto->label,
                'value' => $dto->value,
                'order_index' => $dto->order_index,
            ]);
            return new ServiceResponse($choice, $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function delete(int $id): ServiceResponse
    {
        try {
            $choice = Choice::find($id);
            if (!$choice) {
                return new ServiceResponse(['message' => 'Choice not found'], $this->resourceMap, 404);
            }
            $question = Question::find($choice->question_id);
            if (!$question) {
                return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
            }
            $surveyPage = SurveyPage::find($question->page_id);
            $survey = $surveyPage ? Survey::find($surveyPage->survey_id) : null;
            if (!$survey || $survey->created_by !== Auth::id()) {
                return new ServiceResponse(['message' => 'Forbidden'], $this->resourceMap, 403);
            }
            $choice->delete();
            return new ServiceResponse(['message' => 'Choice deleted successfully'], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getByQuestion(int $questionId): ServiceResponse
    {
        try {
            $question = Question::find($questionId);
            if (!$question) {
                return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
            }
            $choices = $question->choices()->orderBy('order_index')->get();
            return new ServiceResponse(['data' => $choices], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function reorder(int $questionId, array $choices): ServiceResponse
    {
        try {
            $question = Question::find($questionId);
            if (!$question) {
                return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
            }
            $surveyPage = SurveyPage::find($question->page_id);
            $survey = $surveyPage ? Survey::find($surveyPage->survey_id) : null;
            if (!$survey || $survey->created_by !== Auth::id()) {
                return new ServiceResponse(['message' => 'Forbidden'], $this->resourceMap, 403);
            }
            foreach ($choices as $choiceData) {
                $choice = Choice::where('id', $choiceData['id'])
                    ->where('question_id', $questionId)
                    ->first();
                if ($choice) {
                    $choice->update(['order_index' => $choiceData['order_index']]);
                }
            }
            return new ServiceResponse(['message' => 'Choices reordered successfully'], $this->resourceMap, 200);
        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }
} 