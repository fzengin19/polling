<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Services\Abstract\SurveyServiceInterface;
use App\Dtos\SurveyDto;
use App\Dtos\UpdateSurveyDto;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use App\Dtos\SurveyPageDto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SurveyService implements SurveyServiceInterface
{
    public function __construct(
        protected SurveyRepositoryInterface $surveyRepository,
        protected SurveyPageRepositoryInterface $surveyPageRepository,
        protected ResourceMapInterface $resourceMap
    ) {}

    public function create(SurveyDto $dto): ServiceResponse
    {
        $data = $dto->toArray();
        $data['created_by'] = Auth::id();

        $logoMediaId = null;
        if (isset($data['settings']['theme']['logo_media_id'])) {
            $logoMediaId = $data['settings']['theme']['logo_media_id'];
            // logo_media_id'yi settings'de tutuyoruz, çıkarmıyoruz
        }

        $survey = $this->surveyRepository->create($data);

        if ($logoMediaId && ($media = Media::find($logoMediaId))) {
            try {
                $media->copy($survey, 'survey-logos');
            } catch (\Exception $e) {
                // Medya kopyalama başarısız olursa log'la ama işlemi durdurma
                Log::warning("Failed to copy media {$logoMediaId} to survey {$survey->id}: " . $e->getMessage());
            }
        }

        return ServiceResponse::created($survey);
    }

    public function update(int $id, UpdateSurveyDto $dto): ServiceResponse
    {
        $data = $dto->toArray();
        
        $logoMediaId = null;
        if (isset($data['settings']['theme']['logo_media_id'])) {
            $logoMediaId = $data['settings']['theme']['logo_media_id'];
            // logo_media_id'yi settings'de tutuyoruz, çıkarmıyoruz
        }

        $this->surveyRepository->update($id, $data);
        $survey = $this->surveyRepository->find($id);

        if ($logoMediaId && ($media = Media::find($logoMediaId))) {
            try {
                $survey->clearMediaCollection('survey-logos');
                $media->copy($survey, 'survey-logos');
            } catch (\Exception $e) {
                Log::warning("Failed to copy media {$logoMediaId} to survey {$survey->id}: " . $e->getMessage());
            }
        }

        return ServiceResponse::success($survey);
    }

    public function delete(int $id): ServiceResponse
    {
        $this->surveyRepository->delete($id);
        return ServiceResponse::noContent();
    }

    public function find(int $id): ServiceResponse
    {
        $survey = $this->surveyRepository->find($id);

        if (!$survey) {
            return ServiceResponse::notFound('Survey not found.');
        }

        $survey->load('pages');
        return ServiceResponse::success($survey);
    }

    public function getAll(int $perPage = 15): ServiceResponse
    {
        $surveys = $this->surveyRepository->paginate($perPage);
        $surveys->load('pages');
        return ServiceResponse::success($surveys);
    }

    public function getByUser(int $userId): ServiceResponse
    {
        $surveys = $this->surveyRepository->findByUser($userId);
        $surveys->load('pages');
        return ServiceResponse::success($surveys);
    }

    public function getByStatus(string $status): ServiceResponse
    {
        $surveys = $this->surveyRepository->findByStatus($status);
        $surveys->load('pages');
        return ServiceResponse::success($surveys);
    }

    public function getActiveSurveys(): ServiceResponse
    {
        $surveys = $this->surveyRepository->getActiveSurveys();
        $surveys->load('pages');
        return ServiceResponse::success($surveys);
    }

    public function getByTemplate(int $templateId): ServiceResponse
    {
        $surveys = $this->surveyRepository->findByTemplate($templateId);
        $surveys->load('pages');
        return ServiceResponse::success($surveys);
    }

    public function publish(int $id): ServiceResponse
    {
        $survey = $this->surveyRepository->find($id);
        if (!$survey) {
            return ServiceResponse::notFound('Survey not found.');
        }

        if ($survey->status !== 'draft') {
            return ServiceResponse::error('Only draft surveys can be published.', null, 400);
        }

        $this->surveyRepository->update($id, ['status' => 'active']);
        return ServiceResponse::success($survey->fresh());
    }

    public function archive(int $id): ServiceResponse
    {
        $this->surveyRepository->update($id, ['status' => 'archived']);
        return ServiceResponse::success($this->surveyRepository->find($id)->fresh());
    }

    public function duplicate(int $id): ServiceResponse
    {
        $newSurveyId = $this->surveyRepository->duplicate($id);
        $newSurvey = $this->surveyRepository->find($newSurveyId);
        return ServiceResponse::created($newSurvey, 'Survey duplicated successfully.');
    }

    // Page Management Methods
    public function getOrderedPages(int $surveyId): ServiceResponse
    {
        $pages = $this->surveyPageRepository->getOrderedPages($surveyId);
        return ServiceResponse::success($pages);
    }

    public function createPage(SurveyPageDto $dto): ServiceResponse
    {
        $page = $this->surveyPageRepository->create($dto->toDatabaseArray());
        return ServiceResponse::created($page);
    }

    public function findPage(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return ServiceResponse::notFound('Survey page not found.');
        }
        return ServiceResponse::success($page);
    }

    public function updatePage(int $id, SurveyPageDto $dto): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return ServiceResponse::notFound('Survey page not found.');
        }
        $this->surveyPageRepository->update($id, $dto->toDatabaseArray());
        return ServiceResponse::success($this->surveyPageRepository->find($id));
    }

    public function deletePage(int $id): ServiceResponse
    {
        $page = $this->surveyPageRepository->find($id);
        if (!$page) {
            return ServiceResponse::notFound('Survey page not found.');
        }
        $this->surveyPageRepository->delete($id);
        return ServiceResponse::noContent('Survey page deleted successfully.');
    }

    public function reorderPages(int $surveyId, array $pageIds): ServiceResponse
    {
        $this->surveyPageRepository->reorder($pageIds);
        return ServiceResponse::success(null, 'Pages reordered successfully.');
    }
} 