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
        return ServiceResponse::success($pages);
    }

    public function createPage(SurveyPageDto $dto): ServiceResponse
    {
        $data = $dto->toDatabaseArray();
        if (!isset($data['order_index'])) {
            $data['order_index'] = $this->surveyPageRepository->getNextOrderIndex($dto->survey_id);
        }
        $page = $this->surveyPageRepository->create($data);
        return ServiceResponse::created($page);
    }

    public function findPage(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return ServiceResponse::notFound('Page not found.');
        }
        return ServiceResponse::success($page);
    }

    public function updatePage(int $id, array $data): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return ServiceResponse::notFound('Page not found.');
        }
        $this->surveyPageRepository->update($id, $data);
        return ServiceResponse::success($this->surveyPageRepository->find($id));
    }

    public function deletePage(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return ServiceResponse::notFound('Page not found.');
        }
        $this->surveyPageRepository->delete($id);
        return ServiceResponse::noContent('Page deleted successfully.');
    }

    public function reorderPages(int $surveyId, array $pageOrder): ServiceResponse
    {
        $this->surveyPageRepository->reorder($pageOrder);
        return ServiceResponse::success(null, 'Pages reordered successfully.');
    }
}