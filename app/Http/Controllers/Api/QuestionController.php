<?php

namespace App\Http\Controllers\Api;

use App\Dtos\QuestionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CreateQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use App\Services\Abstract\QuestionServiceInterface;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(protected QuestionServiceInterface $questions) {}

    public function index(Request $request, $surveyPageId)
    {
        $result = $this->questions->getBySurveyPage($surveyPageId);
        return $result->toResponse();
    }

    public function show($id)
    {
        $result = $this->questions->findById($id);
        return $result->toResponse();
    }

    public function store(CreateQuestionRequest $request)
    {
        $dto = new QuestionDto($request->validated());
        $result = $this->questions->create($dto);
        return $result->toResponse();
    }

    public function update(UpdateQuestionRequest $request, $id)
    {
        $dto = new QuestionDto($request->validated());
        $result = $this->questions->update($id, $dto);
        return $result->toResponse();
    }

    public function destroy($id)
    {
        $result = $this->questions->delete($id);
        return $result->toResponse();
    }

    public function reorder(Request $request, $surveyPageId)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'integer|exists:questions,id',
        ]);
        $result = $this->questions->reorder($surveyPageId, $request->question_ids);
        return $result->toResponse();
    }

    public function byType(Request $request, $surveyPageId, $type)
    {
        $result = $this->questions->getByType($surveyPageId, $type);
        return $result->toResponse();
    }
} 