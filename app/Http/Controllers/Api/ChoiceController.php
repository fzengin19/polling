<?php

namespace App\Http\Controllers\Api;

use App\Dtos\ChoiceDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Choice\CreateChoiceRequest;
use App\Http\Requests\Choice\UpdateChoiceRequest;
use App\Models\Choice;
use App\Models\Question;
use App\Responses\ServiceResponse;
use App\Services\Abstract\ChoiceServiceInterface;
use App\Services\Abstract\QuestionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Choice Management
 *
 * APIs for managing choices within a question
 */
class ChoiceController extends Controller
{
    public function __construct(
        private readonly ChoiceServiceInterface $choiceService,
        private readonly QuestionServiceInterface $questionService
    ) {
    }

    /**
     * List Choices by Question
     *
     * Get a list of all choices for a specific question.
     * @urlParam questionId required The ID of the question. Example: 1
     * @responseFile storage/app/private/scribe/responses/choices.index.json
     */
    public function index(int $questionId): JsonResponse
    {
        $questionResult = $this->questionService->findQuestion($questionId);
        if ($questionResult->getData() === null) {
            return $questionResult->toResponse();
        }
        $question = $questionResult->getData();
        $this->authorize('view', $question->surveyPage->survey);

        $result = $this->choiceService->getByQuestion($questionId);
        return $result->toResponse();
    }

    /**
     * Create Choice
     *
     * Add a new choice to a specific question.
     * @authenticated
     * @urlParam questionId required The ID of the question. Example: 1
     * @responseFile status=201 storage/app/private/scribe/responses/choices.show.json
     */
    public function store(CreateChoiceRequest $request, int $questionId): JsonResponse
    {
        $questionResult = $this->questionService->findQuestion($questionId);
        if ($questionResult->getData() === null) {
            return $questionResult->toResponse();
        }
        $question = $questionResult->getData();
        $this->authorize('update', $question->surveyPage->survey);

        $data = array_merge($request->validated(), ['question_id' => $questionId]);
        $choiceDto = ChoiceDto::fromArray($data);
        $result = $this->choiceService->createChoice($choiceDto);
        return $result->toResponse();
    }

    /**
     * Get Choice
     *
     * Get the details of a specific choice.
     * @urlParam choice required The ID of the choice. Example: 1
     * @responseFile storage/app/private/scribe/responses/choices.show.json
     */
    public function show(int $id): JsonResponse
    {
        $choiceResult = $this->choiceService->findChoice($id);
        if ($choiceResult->getData() === null) {
            return $choiceResult->toResponse();
        }
        $choice = $choiceResult->getData();
        $this->authorize('view', $choice->question->surveyPage->survey);

        return $choiceResult->toResponse();
    }

    /**
     * Update Choice
     *
     * Update a specific choice.
     * @authenticated
     * @urlParam choice required The ID of the choice. Example: 1
     * @responseFile storage/app/private/scribe/responses/choices.show.json
     */
    public function update(UpdateChoiceRequest $request, int $id): JsonResponse
    {
        $choiceResult = $this->choiceService->findChoice($id);
        if ($choiceResult->getData() === null) {
            return $choiceResult->toResponse();
        }
        $choice = $choiceResult->getData();
        $this->authorize('update', $choice->question->surveyPage->survey);

        $choiceDto = ChoiceDto::fromArray($request->validated());
        $result = $this->choiceService->updateChoice($id, $choiceDto);
        return $result->toResponse();
    }

    /**
     * Delete Choice
     *
     * Delete a specific choice.
     * @authenticated
     * @urlParam choice required The ID of the choice. Example: 1
     * @response 200 {"success": true, "message": "Choice deleted successfully", "data": null}
     */
    public function destroy(int $id): JsonResponse
    {
        $choiceResult = $this->choiceService->findChoice($id);
        if ($choiceResult->getData() === null) {
            return $choiceResult->toResponse();
        }
        $choice = $choiceResult->getData();
        $this->authorize('update', $choice->question->surveyPage->survey);

        $result = $this->choiceService->deleteChoiceById($id);
        return $result->toResponse();
    }

    /**
     * Reorder Choices
     *
     * Reorder the choices within a specific question.
     * @authenticated
     * @urlParam questionId required The ID of the question. Example: 1
     * @response 200 {"success": true, "message": "Choices reordered successfully", "data": null}
     */
    public function reorder(Request $request, int $questionId): JsonResponse
    {
        $questionResult = $this->questionService->findQuestion($questionId);
        if ($questionResult->getData() === null) {
            return $questionResult->toResponse();
        }
        $question = $questionResult->getData();
        $this->authorize('update', $question->surveyPage->survey);

        $validated = $request->validate([
            'choices' => 'required|array',
            'choices.*' => 'integer',
        ]);
        $result = $this->choiceService->reorder($questionId, $validated['choices']);
        return $result->toResponse();
    }
} 