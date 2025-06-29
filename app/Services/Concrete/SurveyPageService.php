<?php

namespace App\Services\Concrete;

use App\Dtos\SurveyPageDto;
use App\Models\SurveyPage;
use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use App\Services\Abstract\SurveyPageServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;

class SurveyPageService implements SurveyPageServiceInterface
{
    protected ResourceMapInterface $resourceMap;

    public function __construct(
        protected SurveyPageRepositoryInterface $surveyPageRepository,
        ResourceMapInterface $resourceMap
    ) {
        $this->resourceMap = $resourceMap;
    }

    public function getPagesBySurveyId(int $surveyId): ServiceResponse
    {
        $pages = $this->surveyPageRepository->getOrderedPages($surveyId);
        return new ServiceResponse($pages, $this->resourceMap);
    }

    public function createPage(SurveyPageDto $dto): ServiceResponse
    {
        $data = $dto->toDatabaseArray();
        if (!isset($data['order_index'])) {
            $data['order_index'] = $this->surveyPageRepository->getNextOrderIndex($dto->survey_id);
        }
        $page = $this->surveyPageRepository->create($data);
        return new ServiceResponse($page, $this->resourceMap, 201);
    }

    public function findPage(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        $status = $page ? 200 : 404;
        return new ServiceResponse($page, $this->resourceMap, $status);
    }

    public function updatePage(int $id, array $data): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return new ServiceResponse(null, $this->resourceMap, 404);
        }
        $this->surveyPageRepository->update($id, $data);
        $updatedPage = $this->surveyPageRepository->find($id);
        return new ServiceResponse($updatedPage, $this->resourceMap);
    }

    public function deletePage(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return new ServiceResponse(null, $this->resourceMap, 404);
        }
        $this->surveyPageRepository->delete($id);
        return new ServiceResponse(null, $this->resourceMap, 204);
    }

    public function reorderPages(int $surveyId, array $pageIds): ServiceResponse
    {
        $this->surveyPageRepository->reorder($pageIds);
        
        return new ServiceResponse(['message' => 'Pages reordered successfully.'], $this->resourceMap);
    }
}