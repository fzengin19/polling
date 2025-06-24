<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Services\Abstract\SurveyServiceInterface;
use App\Dtos\SurveyDto;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SurveyService implements SurveyServiceInterface
{
    public function __construct(
        protected SurveyRepositoryInterface $surveyRepository,
        protected ResourceMapInterface $resourceMap
    ) {}

    public function create(SurveyDto $dto): ServiceResponse
    {
        $survey = $this->surveyRepository->create($dto->toArray());

        return new ServiceResponse($survey, $this->resourceMap, 201);
    }

    public function update(int $id, SurveyDto $dto): ServiceResponse
    {
        $survey = $this->surveyRepository->find($id);

        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        // Check if user owns this survey
        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $this->surveyRepository->update($survey, $dto->toArray());

        return new ServiceResponse($survey->fresh(), $this->resourceMap);
    }

    public function delete(int $id): ServiceResponse
    {
        $survey = $this->surveyRepository->find($id);

        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        // Check if user owns this survey
        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $this->surveyRepository->delete($survey);

        return new ServiceResponse(['message' => 'Survey deleted successfully.'], $this->resourceMap);
    }

    public function find(int $id): ServiceResponse
    {
        $survey = $this->surveyRepository->find($id);

        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        // Check if user can access this survey (owner or public)
        if ($survey->created_by !== Auth::id() && $survey->status !== 'active') {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        return new ServiceResponse($survey, $this->resourceMap);
    }

    public function getAll(): ServiceResponse
    {
        $surveys = $this->surveyRepository->all();

        return new ServiceResponse($surveys, $this->resourceMap);
    }

    public function getByUser(int $userId): ServiceResponse
    {
        $surveys = $this->surveyRepository->findByUser($userId);

        return new ServiceResponse($surveys, $this->resourceMap);
    }

    public function getByStatus(string $status): ServiceResponse
    {
        $surveys = $this->surveyRepository->findByStatus($status);

        return new ServiceResponse($surveys, $this->resourceMap);
    }

    public function getActiveSurveys(): ServiceResponse
    {
        $surveys = $this->surveyRepository->getActiveSurveys();

        return new ServiceResponse($surveys, $this->resourceMap);
    }

    public function getByTemplate(int $templateId): ServiceResponse
    {
        $surveys = $this->surveyRepository->findByTemplate($templateId);

        return new ServiceResponse($surveys, $this->resourceMap);
    }

    public function publish(int $id): ServiceResponse
    {
        $survey = $this->surveyRepository->find($id);

        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        // Check if user owns this survey
        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        // Check if survey is in draft status
        if ($survey->status !== 'draft') {
            return new ServiceResponse(['message' => 'Only draft surveys can be published.'], $this->resourceMap, 400);
        }

        $this->surveyRepository->update($survey, ['status' => 'active']);

        return new ServiceResponse($survey->fresh(), $this->resourceMap);
    }

    public function archive(int $id): ServiceResponse
    {
        $survey = $this->surveyRepository->find($id);

        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        // Check if user owns this survey
        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $this->surveyRepository->update($survey, ['status' => 'archived']);

        return new ServiceResponse($survey->fresh(), $this->resourceMap);
    }

    public function duplicate(int $id): ServiceResponse
    {
        $originalSurvey = $this->surveyRepository->find($id);

        if (!$originalSurvey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        // Check if user can access this survey (owner or public)
        if ($originalSurvey->created_by !== Auth::id() && $originalSurvey->status !== 'active') {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $duplicatedSurvey = $this->surveyRepository->create([
            'title' => $originalSurvey->title . ' (Copy)',
            'description' => $originalSurvey->description,
            'status' => 'draft', // Duplicated surveys start as draft
            'created_by' => Auth::id(),
            'template_id' => $originalSurvey->template_id,
            'template_version_id' => $originalSurvey->template_version_id,
            'settings' => $originalSurvey->settings,
            'expires_at' => null, // Reset expiration date
            'max_responses' => $originalSurvey->max_responses,
        ]);

        return new ServiceResponse($duplicatedSurvey, $this->resourceMap, 201);
    }
} 