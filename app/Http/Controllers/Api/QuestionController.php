<?php

namespace App\Http\Controllers\Api;

use App\Dtos\QuestionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CreateQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use App\Services\Abstract\QuestionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(
        private readonly QuestionServiceInterface $questionService
    ) {
    }

    public function index(int $pageId): JsonResponse
    {
        $result = $this->questionService->getQuestionsByPageId($pageId);
        return $result->toResponse();
    }

    public function store(CreateQuestionRequest $request, int $pageId): JsonResponse
    {
        $data = array_merge($request->validated(), ['page_id' => $pageId]);
        $questionDto = QuestionDto::fromArray($data);
        $result = $this->questionService->createQuestion($questionDto);
        return $result->toResponse();
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->questionService->findQuestion($id);
        return $result->toResponse();
    }

    public function update(UpdateQuestionRequest $request, int $id): JsonResponse
    {
        $questionDto = QuestionDto::fromArray($request->validated());
        $result = $this->questionService->updateQuestion($id, $questionDto);
        return $result->toResponse();
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->questionService->deleteQuestionById($id);
        return $result->toResponse();
    }

    public function reorder(Request $request, int $pageId): JsonResponse
    {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'integer',
        ]);
        $result = $this->questionService->reorder($pageId, $validated['question_ids']);
        return $result->toResponse();
    }
} 