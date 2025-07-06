<?php

namespace App\Http\Controllers\Api;

use App\Dtos\QuestionDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\CreateQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\SurveyPage;
use App\Services\Abstract\QuestionServiceInterface;
use App\Services\Abstract\SurveyPageServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Question Management
 *
 * APIs for managing questions within a survey page
 */
class QuestionController extends Controller
{
    public function __construct(
        private QuestionServiceInterface $questionService,
        private SurveyPageServiceInterface $surveyPageService
    ) {
    }

    /**
     * List Questions by Page
     *
     * Get a list of all questions for a specific survey page.
     * @urlParam pageId required The ID of the survey page. Example: 1
     * @responseFile storage/app/private/scribe/responses/questions.index.json
     */
    public function index(int $pageId): JsonResponse
    {
        // Authorization can be handled by a policy on SurveyPage
        $result = $this->questionService->getQuestionsByPageId($pageId);
        return $result->toResponse();
    }

    /**
     * Create Question
     *
     * Add a new question to a specific survey page.
     * @authenticated
     * @urlParam pageId required The ID of the survey page. Example: 1
     * @responseFile status=201 storage/app/private/scribe/responses/questions.show.json
     */
    public function store(CreateQuestionRequest $request, int $pageId): JsonResponse
    {
        $pageResult = $this->surveyPageService->findPage($pageId);
        if ($pageResult->getData() === null) {
            return $pageResult->toResponse();
        }
        $page = $pageResult->getData();
        $this->authorize('update', $page->survey);

        $data = array_merge($request->validated(), ['page_id' => $pageId]);
        $dto = new QuestionDto(...$data);
        $result = $this->questionService->createQuestion($dto);
        return $result->toResponse();
    }

    /**
     * Get Question
     *
     * Get the details of a specific question.
     * @urlParam id required The ID of the question. Example: 1
     * @responseFile storage/app/private/scribe/responses/questions.show.json
     */
    public function show(int $id): JsonResponse
    {
        $questionResult = $this->questionService->findQuestion($id);
        if ($questionResult->getData() === null) {
            return $questionResult->toResponse();
        }
        $question = $questionResult->getData();
        $this->authorize('view', $question->surveyPage->survey);

        return $questionResult->toResponse();
    }

    /**
     * Update Question
     *
     * Update a specific question.
     * @authenticated
     * @urlParam id required The ID of the question. Example: 1
     * @responseFile storage/app/private/scribe/responses/questions.show.json
     */
    public function update(UpdateQuestionRequest $request, int $id): JsonResponse
    {
        $questionResult = $this->questionService->findQuestion($id);
        if ($questionResult->getData() === null) {
            return $questionResult->toResponse();
        }
        $question = $questionResult->getData();
        $this->authorize('update', $question->surveyPage->survey);

        $dto = new QuestionDto(...$request->validated());
        $result = $this->questionService->updateQuestion($id, $dto);
        return $result->toResponse();
    }

    /**
     * Delete Question
     *
     * Delete a specific question.
     * @authenticated
     * @urlParam id required The ID of the question. Example: 1
     * @response 200 {"success": true, "message": "Question deleted successfully", "data": null}
     */
    public function destroy(int $id): JsonResponse
    {
        $questionResult = $this->questionService->findQuestion($id);
        if ($questionResult->getData() === null) {
            return $questionResult->toResponse();
        }
        $question = $questionResult->getData();
        $this->authorize('update', $question->surveyPage->survey);

        $result = $this->questionService->deleteQuestionById($id);
        return $result->toResponse();
    }

    /**
     * Reorder Questions
     *
     * Reorder the questions within a specific page.
     * @authenticated
     * @urlParam pageId required The ID of the survey page. Example: 1
     * @response 200 {"success": true, "message": "Questions reordered successfully", "data": null}
     */
    public function reorder(Request $request, int $pageId): JsonResponse
    {
        $pageResult = $this->surveyPageService->findPage($pageId);
        if ($pageResult->getData() === null) {
            return $pageResult->toResponse();
        }
        $page = $pageResult->getData();
        $this->authorize('update', $page->survey);

        $result = $this->questionService->reorder($pageId, $request->input('question_ids'));
        return $result->toResponse();
    }
} 