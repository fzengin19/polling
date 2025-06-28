<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Choice\CreateChoiceRequest;
use App\Http\Requests\Choice\UpdateChoiceRequest;
use App\Services\Abstract\ChoiceServiceInterface;
use App\Dtos\ChoiceDto;
use App\Models\Choice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChoiceController extends Controller
{
    public function __construct(
        private ChoiceServiceInterface $choiceService
    ) {}

    public function store(CreateChoiceRequest $request): JsonResponse
    {
        $dto = new ChoiceDto(
            id: null,
            question_id: $request->validated('question_id'),
            label: $request->validated('label'),
            value: $request->validated('value'),
            order_index: $request->validated('order_index', 0),
        );

        $result = $this->choiceService->create($dto);
        return $result->toResponse();
    }

    public function show(int $id): JsonResponse
    {
        $choice = Choice::find($id);
        if (!$choice) {
            return response()->json(['message' => 'Choice not found'], 404);
        }
        return response()->json($choice);
    }

    public function update(UpdateChoiceRequest $request, int $id): JsonResponse
    {
        $dto = new ChoiceDto(
            id: $id,
            question_id: 0, // This will be ignored in update
            label: $request->validated('label'),
            value: $request->validated('value'),
            order_index: $request->validated('order_index', 0),
        );

        $result = $this->choiceService->update($id, $dto);
        return $result->toResponse();
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->choiceService->delete($id);
        return $result->toResponse();
    }

    public function getByQuestion(int $questionId): JsonResponse
    {
        $result = $this->choiceService->getByQuestion($questionId);
        return $result->toResponse();
    }

    public function reorder(Request $request, int $questionId): JsonResponse
    {
        $request->validate([
            'choices' => 'required|array',
            'choices.*.id' => 'required|integer|exists:choices,id',
            'choices.*.order_index' => 'required|integer|min:0',
        ]);

        $result = $this->choiceService->reorder($questionId, $request->choices);
        return $result->toResponse();
    }
} 