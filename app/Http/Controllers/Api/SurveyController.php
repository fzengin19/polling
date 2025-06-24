<?php

namespace App\Http\Controllers\Api;

use App\Services\Abstract\SurveyServiceInterface;
use App\Http\Requests\Survey\CreateSurveyRequest;
use App\Http\Requests\Survey\UpdateSurveyRequest;
use App\Dtos\SurveyDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    public function __construct(
        protected SurveyServiceInterface $surveyService
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
} 