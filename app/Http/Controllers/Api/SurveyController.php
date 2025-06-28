<?php

namespace App\Http\Controllers\Api;

use App\Services\Abstract\SurveyServiceInterface;
use App\Services\Abstract\SurveyPageServiceInterface;
use App\Http\Requests\Survey\CreateSurveyRequest;
use App\Http\Requests\Survey\UpdateSurveyRequest;
use App\Http\Requests\SurveyPage\CreateSurveyPageRequest;
use App\Http\Requests\SurveyPage\UpdateSurveyPageRequest;
use App\Dtos\SurveyDto;
use App\Dtos\SurveyPageDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Abstract\ResponseServiceInterface;

class SurveyController extends Controller
{
    public function __construct(
        protected SurveyServiceInterface $surveyService,
        protected SurveyPageServiceInterface $surveyPageService,
        protected ResponseServiceInterface $responseService
    ) {}

    public function index(): JsonResponse
    {
        $result = $this->surveyService->getAll();
        return $result->toResponse();
    }

    public function store(CreateSurveyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        
        $dto = SurveyDto::fromArray($data);
        $result = $this->surveyService->create($dto);
        return $result->toResponse();
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->surveyService->find($id);
        return $result->toResponse();
    }

    public function update(UpdateSurveyRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        
        $dto = SurveyDto::fromArray($data);
        $result = $this->surveyService->update($id, $dto);
        return $result->toResponse();
    }

    public function destroy(int $id): JsonResponse
    {
        $result = $this->surveyService->delete($id);
        return $result->toResponse();
    }

    public function mySurveys(): JsonResponse
    {
        $result = $this->surveyService->getByUser(Auth::id());
        return $result->toResponse();
    }

    public function activeSurveys(): JsonResponse
    {
        $result = $this->surveyService->getActiveSurveys();
        return $result->toResponse();
    }

    public function byStatus(string $status): JsonResponse
    {
        $result = $this->surveyService->getByStatus($status);
        return $result->toResponse();
    }

    public function byTemplate(int $templateId): JsonResponse
    {
        $result = $this->surveyService->getByTemplate($templateId);
        return $result->toResponse();
    }

    public function publish(int $id): JsonResponse
    {
        $result = $this->surveyService->publish($id);
        return $result->toResponse();
    }

    public function archive(int $id): JsonResponse
    {
        $result = $this->surveyService->archive($id);
        return $result->toResponse();
    }

    public function duplicate(int $id): JsonResponse
    {
        $result = $this->surveyService->duplicate($id);
        return $result->toResponse();
    }

    public function responseStatistics(int $id): JsonResponse
    {
        $result = $this->responseService->getStatistics($id);
        return $result->toResponse();
    }

    // Survey Page Management
    public function pages(int $surveyId): JsonResponse
    {
        $result = $this->surveyPageService->getOrderedPages($surveyId);
        return $result->toResponse();
    }

    public function storePage(CreateSurveyPageRequest $request): JsonResponse
    {
        $dto = SurveyPageDto::fromArray($request->validated());
        $result = $this->surveyPageService->create($dto);
        return $result->toResponse();
    }

    public function showPage(int $id): JsonResponse
    {
        $result = $this->surveyPageService->find($id);
        return $result->toResponse();
    }

    public function updatePage(UpdateSurveyPageRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        
        // Get the existing page to get survey_id
        $page = \App\Models\SurveyPage::find($id);
        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }
        
        $data['survey_id'] = $page->survey_id;
        $dto = SurveyPageDto::fromArray($data);
        $result = $this->surveyPageService->update($id, $dto);
        return $result->toResponse();
    }

    public function destroyPage(int $id): JsonResponse
    {
        $result = $this->surveyPageService->delete($id);
        return $result->toResponse();
    }

    public function reorderPages(Request $request, int $surveyId): JsonResponse
    {
        $request->validate([
            'page_ids' => 'required|array',
            'page_ids.*' => 'integer|exists:survey_pages,id'
        ]);

        $result = $this->surveyPageService->reorder($surveyId, $request->page_ids);
        return $result->toResponse();
    }
} 