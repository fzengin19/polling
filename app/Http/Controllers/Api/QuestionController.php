<?php

namespace App\Http\Controllers\Api;

use App\Dtos\QuestionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CreateQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Services\Abstract\QuestionServiceInterface;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(protected QuestionServiceInterface $questions) {}

    public function index(Request $request, $surveyPageId)
    {
        $questions = $this->questions->getBySurveyPage($surveyPageId);
        return QuestionResource::collection($questions);
    }

    public function show($id)
    {
        $question = $this->questions->findById($id);
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }
        return new QuestionResource($question);
    }

    public function store(CreateQuestionRequest $request)
    {
        $dto = new QuestionDto($request->validated());
        $question = $this->questions->create($dto);
        return new QuestionResource($question);
    }

    public function update(UpdateQuestionRequest $request, $id)
    {
        $question = $this->questions->findById($id);
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }
        $dto = new QuestionDto(array_merge($question->toArray(), $request->validated()));
        $question = $this->questions->update($question, $dto);
        return new QuestionResource($question);
    }

    public function destroy($id)
    {
        $question = $this->questions->findById($id);
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }
        $this->questions->delete($question);
        return response()->json(['message' => 'Question deleted']);
    }

    public function reorder(Request $request, $surveyPageId)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);
        $this->questions->reorder($surveyPageId, $request->question_ids);
        return response()->json(['message' => 'Questions reordered']);
    }

    public function byType(Request $request, $surveyPageId, $type)
    {
        $questions = $this->questions->getByType($surveyPageId, $type);
        return QuestionResource::collection($questions);
    }
} 