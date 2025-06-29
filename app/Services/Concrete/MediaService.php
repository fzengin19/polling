<?php

namespace App\Services\Concrete;

use App\Dtos\MediaDto;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use App\Services\Abstract\MediaServiceInterface;
use App\Repositories\Abstract\SurveyRepositoryInterface;
use App\Repositories\Abstract\SurveyPageRepositoryInterface;
use App\Repositories\Abstract\QuestionRepositoryInterface;
use App\Repositories\Abstract\ChoiceRepositoryInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MediaService implements MediaServiceInterface
{
    public function __construct(
        protected ResourceMapInterface $resourceMap,
        protected SurveyRepositoryInterface $surveyRepository,
        protected SurveyPageRepositoryInterface $surveyPageRepository,
        protected QuestionRepositoryInterface $questionRepository,
        protected ChoiceRepositoryInterface $choiceRepository
    ) {}

    public function uploadMedia(MediaDto $dto): ServiceResponse
    {
        try {
            DB::beginTransaction();

            $question = $this->questionRepository->find($dto->question_id);
            if (!$question) {
                return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
            }

            // MediaLibrary will handle the file upload and storage
            $media = $question->addMediaFromRequest('file')
                ->withCustomProperties([
                    'alt_text' => $dto->alt_text,
                    'caption' => $dto->caption,
                ])
                ->toMediaCollection('question-media');

            DB::commit();
            return new ServiceResponse($media, $this->resourceMap, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function uploadMediaForModel(string $modelType, int $modelId, UploadedFile $file, string $collection): ServiceResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);
            if (!$model) {
                return new ServiceResponse(['message' => 'Model not found'], $this->resourceMap, 404);
            }

            DB::beginTransaction();

            $media = $model->addMedia($file)
                ->toMediaCollection($collection);

            DB::commit();
            return new ServiceResponse($media, $this->resourceMap, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function deleteMedia(int $mediaId): ServiceResponse
    {
        try {
            $media = Media::find($mediaId);
            if (!$media) {
                return new ServiceResponse(['message' => 'Media not found'], $this->resourceMap, 404);
            }

            $media->delete();
            return new ServiceResponse(['message' => 'Media deleted successfully'], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getQuestionMedia(int $questionId): ServiceResponse
    {
        try {
            $question = $this->questionRepository->find($questionId);
            if (!$question) {
                return new ServiceResponse(['message' => 'Question not found'], $this->resourceMap, 404);
            }

            $media = $question->getMedia('question-media');
            return new ServiceResponse($media, $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getMedia(string $modelType, int $modelId, ?string $collection = null): ServiceResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);
            if (!$model) {
                return new ServiceResponse(['message' => 'Model not found'], $this->resourceMap, 404);
            }

            if ($collection) {
                $media = $model->getMedia($collection);
            } else {
                $media = $model->getMedia();
            }
            
            return new ServiceResponse($media, $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function updateMediaMetadata(int $mediaId, array $metadata): ServiceResponse
    {
        try {
            $media = Media::find($mediaId);
            if (!$media) {
                return new ServiceResponse(['message' => 'Media not found'], $this->resourceMap, 404);
            }

            Log::info('Updating media metadata', [
                'media_id' => $mediaId,
                'incoming_metadata' => $metadata,
                'display_order_value' => $metadata['display_order'] ?? 'not_set',
                'display_order_type' => gettype($metadata['display_order'] ?? null)
            ]);
            
            $media->setCustomProperty('alt_text', $metadata['alt_text'] ?? null);
            $media->setCustomProperty('caption', $metadata['caption'] ?? null);
            $displayOrder = isset($metadata['display_order']) ? (int)$metadata['display_order'] : 0;
            $media->setCustomProperty('display_order', $displayOrder);
            
            Log::info('Setting display_order', [
                'display_order' => $displayOrder,
                'display_order_type' => gettype($displayOrder)
            ]);
            
            $media->save();
            $media->refresh();
            
            Log::info('After save and refresh', [
                'custom_properties' => $media->custom_properties,
                'display_order_from_properties' => $media->getCustomProperty('display_order')
            ]);
            
            return new ServiceResponse($media, $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getCollections(string $modelType): ServiceResponse
    {
        try {
            $collections = match ($modelType) {
                'survey' => ['survey-banners', 'survey-logos', 'survey-attachments'],
                'survey-page' => ['page-images', 'page-backgrounds'],
                'question' => ['question-images', 'question-videos', 'question-documents'],
                'choice' => ['choice-images', 'choice-icons'],
                default => []
            };

            return new ServiceResponse($collections, $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['message' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    private function getModel(string $modelType, int $modelId)
    {
        return match ($modelType) {
            'survey' => $this->surveyRepository->find($modelId),
            'survey-page' => $this->surveyPageRepository->find($modelId),
            'question' => $this->questionRepository->find($modelId),
            'choice' => $this->choiceRepository->find($modelId),
            default => null
        };
    }
} 