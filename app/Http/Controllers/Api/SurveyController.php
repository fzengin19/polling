<?php

namespace App\Http\Controllers\Api;

use App\Services\Abstract\SurveyServiceInterface;
use App\Services\Abstract\SurveyPageServiceInterface;
use App\Http\Requests\Survey\CreateSurveyRequest;
use App\Http\Requests\Survey\UpdateSurveyRequest;
use App\Http\Requests\SurveyPage\CreateSurveyPageRequest;
use App\Http\Requests\SurveyPage\UpdateSurveyPageRequest;
use App\Http\Requests\SurveyPage\ReorderSurveyPagesRequest;
use App\Dtos\SurveyDto;
use App\Dtos\UpdateSurveyDto;
use App\Dtos\SurveyPageDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Abstract\ResponseServiceInterface;
use App\Services\Abstract\ServiceResponse;
use App\Services\Abstract\ApiResourceMap;
use App\Http\Resources\SurveyResource;
use App\Http\Resources\SurveyPageResource;
use App\Models\Survey;
use App\Responses\ServiceResponse as AppServiceResponse;

/**
 * @group Survey Management
 *
 * APIs for managing surveys
 */
class SurveyController extends Controller
{
    public function __construct(
        protected SurveyServiceInterface $surveyService,
        protected SurveyPageServiceInterface $surveyPageService,
        protected ResponseServiceInterface $responseService
    ) {}

    /**
     * List Surveys
     *
     * Get a paginated list of surveys.
     * @authenticated
     * @responseFile storage/app/private/scribe/responses/surveys.index.json
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Survey::class);
        $perPage = $request->input('per_page', 15);
        $result = $this->surveyService->getAll($perPage);
        return $result->toResponse();
    }

    /**
     * Create Survey
     *
     * Create a new survey.
     * @authenticated
     * @responseFile status=201 storage/app/private/scribe/responses/surveys.show.json
     */
    public function store(CreateSurveyRequest $request): JsonResponse
    {
        $this->authorize('create', Survey::class);

        $validatedData = $request->validated();
        $validatedData['created_by'] = Auth::id();

        $dto = new SurveyDto(...$validatedData);

        $result = $this->surveyService->create($dto);
        return $result->toResponse();
    }

    /**
     * Get Survey
     *
     * Get the details of a specific survey.
     * @authenticated
     * @urlParam id required The ID of the survey. Example: 1
     * @responseFile storage/app/private/scribe/responses/surveys.show.json
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->surveyService->find($id);

        if ($result->getData() === null) {
            return $result->toResponse();
        }

        $this->authorize('view', $result->getData());
        return $result->toResponse();
    }

    /**
     * Update Survey
     *
     * Update the details of a specific survey.
     * @authenticated
     * @urlParam id required The ID of the survey. Example: 1
     * @responseFile storage/app/private/scribe/responses/surveys.show.json
     */
    public function update(UpdateSurveyRequest $request, int $id): JsonResponse
    {
        $surveyResult = $this->surveyService->find($id);
        if ($surveyResult->getData() === null) {
            return $surveyResult->toResponse();
        }
        $this->authorize('update', $surveyResult->getData());
        
        $dto = UpdateSurveyDto::fromArray($request->validated());
        $result = $this->surveyService->update($id, $dto);
        return $result->toResponse();
    }

    /**
     * Delete Survey
     *
     * Delete a specific survey.
     * @authenticated
     * @urlParam id required The ID of the survey. Example: 1
     * @response 200 {"success": true, "message": "Survey deleted successfully", "data": null}
     */
    public function destroy(int $id): JsonResponse
    {
        $survey = $this->surveyService->find($id)->getData();
        $this->authorize('delete', $survey);
        
        $result = $this->surveyService->delete($id);
        return $result->toResponse();
    }

    /**
     * Get My Surveys
     *
     * Get surveys created by the authenticated user.
     * @authenticated
     * @responseFile storage/app/private/scribe/responses/surveys.index.json
     */
    public function mySurveys(): JsonResponse
    {
        $result = $this->surveyService->getByUser(Auth::id());
        return $result->toResponse();
    }

    /**
     * Get Active Surveys
     *
     * Get all currently active surveys.
     * @responseFile storage/app/private/scribe/responses/surveys.index.json
     */
    public function activeSurveys(): JsonResponse
    {
        $result = $this->surveyService->getActiveSurveys();
        return $result->toResponse();
    }

    /**
     * Get Surveys by Status
     *
     * Get surveys filtered by status.
     * @urlParam status required The status to filter by. Example: active
     * @responseFile storage/app/private/scribe/responses/surveys.index.json
     */
    public function byStatus(string $status): JsonResponse
    {
        $result = $this->surveyService->getByStatus($status);
        return $result->toResponse();
    }

    /**
     * Get Surveys by Template
     *
     * Get surveys created from a specific template.
     * @urlParam templateId required The ID of the template. Example: 1
     * @responseFile storage/app/private/scribe/responses/surveys.index.json
     */
    public function byTemplate(int $templateId): JsonResponse
    {
        $result = $this->surveyService->getByTemplate($templateId);
        return $result->toResponse();
    }

    /**
     * Publish Survey
     *
     * Make a survey active and available for responses.
     * @authenticated
     * @urlParam id required The ID of the survey. Example: 1
     * @responseFile storage/app/private/scribe/responses/surveys.show.json
     */
    public function publish(int $id): JsonResponse
    {
        $survey = $this->surveyService->find($id)->getData();
        $this->authorize('update', $survey);

        $result = $this->surveyService->publish($id);
        return $result->toResponse();
    }

    /**
     * Archive Survey
     *
     * Archive a survey and stop accepting responses.
     * @authenticated
     * @urlParam id required The ID of the survey. Example: 1
     * @responseFile storage/app/private/scribe/responses/surveys.show.json
     */
    public function archive(int $id): JsonResponse
    {
        $survey = $this->surveyService->find($id)->getData();
        $this->authorize('update', $survey);

        $result = $this->surveyService->archive($id);
        return $result->toResponse();
    }

    /**
     * Duplicate Survey
     *
     * Create a copy of an existing survey.
     * @authenticated
     * @urlParam id required The ID of the survey to duplicate. Example: 1
     * @responseFile status=201 storage/app/private/scribe/responses/surveys.show.json
     */
    public function duplicate(int $id): JsonResponse
    {
        $survey = $this->surveyService->find($id)->getData();
        $this->authorize('view', $survey); // A user can duplicate a survey they can view

        $result = $this->surveyService->duplicate($id);
        return $result->toResponse();
    }

    /**
     * Get Response Statistics
     *
     * Get statistical data about survey responses.
     * @authenticated
     * @urlParam id required The ID of the survey. Example: 1
     * @response 200 {"success": true, "data": {"total_responses": 42, "completion_rate": 85.5}}
     */
    public function responseStatistics(int $id): JsonResponse
    {
        $result = $this->responseService->getStatistics($id);
        return $result->toResponse();
    }

    // Survey Page Management
    /**
     * Get Survey Pages
     *
     * Get all pages for a specific survey.
     * @authenticated
     * @urlParam surveyId required The ID of the survey. Example: 1
     * @responseFile storage/app/private/scribe/responses/survey_pages.index.json
     */
    public function pages(int $surveyId): JsonResponse
    {
        $result = $this->surveyPageService->getPagesBySurveyId($surveyId);
        return $result->toResponse();
    }

    /**
     * Create Survey Page
     *
     * Create a new page for a survey.
     * @authenticated
     * @responseFile status=201 storage/app/private/scribe/responses/survey_pages.show.json
     */
    public function storePage(CreateSurveyPageRequest $request): JsonResponse
    {
        $dto = SurveyPageDto::fromArray($request->validated());
        $result = $this->surveyPageService->createPage($dto);
        return $result->toResponse();
    }

    /**
     * Get Survey Page
     *
     * Get the details of a specific survey page.
     * @authenticated
     * @urlParam id required The ID of the survey page. Example: 1
     * @responseFile storage/app/private/scribe/responses/survey_pages.show.json
     */
    public function showPage(int $id): JsonResponse
    {
        $result = $this->surveyPageService->findPage($id);
        return $result->toResponse();
    }

    /**
     * Update Survey Page
     *
     * Update the details of a specific survey page.
     * @authenticated
     * @urlParam id required The ID of the survey page. Example: 1
     * @responseFile storage/app/private/scribe/responses/survey_pages.show.json
     */
    public function updatePage(UpdateSurveyPageRequest $request, int $id): JsonResponse
    {
        $page = $this->surveyPageService->findPage($id)->getData();
        $this->authorize('update', $page->survey);
        
        $result = $this->surveyPageService->updatePage($id, $request->validated());
        return $result->toResponse();
    }

    /**
     * Delete Survey Page
     *
     * Delete a specific survey page.
     * @authenticated
     * @urlParam id required The ID of the survey page. Example: 1
     * @response 200 {"success": true, "message": "Survey page deleted successfully", "data": null}
     */
    public function destroyPage(int $id): JsonResponse
    {
        $page = $this->surveyPageService->findPage($id)->getData();
        $this->authorize('update', $page->survey);
        
        $result = $this->surveyPageService->deletePage($id);
        return $result->toResponse();
    }

    /**
     * Reorder Survey Pages
     *
     * Change the order of pages within a survey.
     * @authenticated
     * @urlParam surveyId required The ID of the survey. Example: 1
     * @response 200 {"success": true, "message": "Survey pages reordered successfully", "data": null}
     */
    public function reorderPages(ReorderSurveyPagesRequest $request, int $surveyId): JsonResponse
    {
        $survey = $this->surveyService->find($surveyId)->getData();
        $this->authorize('update', $survey);
        
        $result = $this->surveyPageService->reorderPages($surveyId, $request->validated()['page_ids']);
        return $result->toResponse();
    }
} 