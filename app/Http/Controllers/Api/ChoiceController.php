<?php

namespace App\Http\Controllers\Api;

use App\Dtos\ChoiceDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Choice\CreateChoiceRequest;
use App\Http\Requests\Choice\UpdateChoiceRequest;
use App\Services\Abstract\ChoiceServiceInterface;
use App\Services\Abstract\QuestionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChoiceController extends Controller
{
    public function __construct(
        private readonly ChoiceServiceInterface $choiceService,
        private readonly QuestionServiceInterface $questionService
    ) {
    }

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