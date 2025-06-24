<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Services\Abstract\SurveyPageServiceInterface;
use App\Dtos\SurveyPageDto;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SurveyPageService implements SurveyPageServiceInterface
{
    public function __construct(
        protected SurveyPageRepositoryInterface $surveyPageRepository,
        protected SurveyRepositoryInterface $surveyRepository,
        protected ResourceMapInterface $resourceMap
    ) {}

    public function create(SurveyPageDto $dto): ServiceResponse
    {
        // Check if survey exists and user owns it
        $survey = $this->surveyRepository->find($dto->surveyId);
        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        // Set order index if not provided
        $data = $dto->toArray();
        if (!isset($data['order_index'])) {
            $data['order_index'] = $this->surveyPageRepository->getNextOrderIndex($dto->surveyId);
        }

        $page = $this->surveyPageRepository->create($data);

        return new ServiceResponse($page, $this->resourceMap, 201);
    }

    public function update(int $id, SurveyPageDto $dto): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);

        if (!$page) {
            throw new ModelNotFoundException('Survey page not found.');
        }

        // Check if user owns the survey
        $survey = $this->surveyRepository->find($page->survey_id);
        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $this->surveyPageRepository->update($page, $dto->toArray());

        return new ServiceResponse($page->fresh(), $this->resourceMap);
    }

    public function delete(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);

        if (!$page) {
            throw new ModelNotFoundException('Survey page not found.');
        }

        // Check if user owns the survey
        $survey = $this->surveyRepository->find($page->survey_id);
        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $this->surveyPageRepository->delete($page);

        return new ServiceResponse(['message' => 'Survey page deleted successfully.'], $this->resourceMap);
    }

    public function find(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);

        if (!$page) {
            throw new ModelNotFoundException('Survey page not found.');
        }

        // Check if user can access the survey
        $survey = $this->surveyRepository->find($page->survey_id);
        if ($survey->created_by !== Auth::id() && $survey->status !== 'active') {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        return new ServiceResponse($page, $this->resourceMap);
    }

    public function getBySurvey(int $surveyId): ServiceResponse
    {
        // Check if user can access the survey
        $survey = $this->surveyRepository->find($surveyId);
        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        if ($survey->created_by !== Auth::id() && $survey->status !== 'active') {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $pages = $this->surveyPageRepository->findBySurvey($surveyId);

        return new ServiceResponse($pages, $this->resourceMap);
    }

    public function getOrderedPages(int $surveyId): ServiceResponse
    {
        // Check if user can access the survey
        $survey = $this->surveyRepository->find($surveyId);
        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        if ($survey->created_by !== Auth::id() && $survey->status !== 'active') {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        $pages = $this->surveyPageRepository->getOrderedPages($surveyId);

        return new ServiceResponse($pages, $this->resourceMap);
    }

    public function reorder(int $surveyId, array $pageIds): ServiceResponse
    {
        // Check if user owns the survey
        $survey = $this->surveyRepository->find($surveyId);
        if (!$survey) {
            throw new ModelNotFoundException('Survey not found.');
        }

        if ($survey->created_by !== Auth::id()) {
            return new ServiceResponse(['message' => 'Unauthorized.'], $this->resourceMap, 403);
        }

        // Verify all pages belong to this survey
        $existingPages = $this->surveyPageRepository->findBySurvey($surveyId);
        $existingPageIds = $existingPages->pluck('id')->toArray();
        
        if (count(array_diff($pageIds, $existingPageIds)) > 0) {
            return new ServiceResponse(['message' => 'Invalid page IDs provided.'], $this->resourceMap, 400);
        }

        $this->surveyPageRepository->reorder($surveyId, $pageIds);

        return new ServiceResponse(['message' => 'Pages reordered successfully.'], $this->resourceMap);
    }
} 