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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\MediaConfigHelper;

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
                return ServiceResponse::notFound('Question not found');
            }

            // MediaLibrary will handle the file upload and storage
            $media = $question->addMediaFromRequest('file')
                ->withCustomProperties([
                    'alt_text' => $dto->alt_text,
                    'caption' => $dto->caption,
                ])
                ->toMediaCollection('question-media');

            DB::commit();
            return ServiceResponse::created($media);

        } catch (\Exception $e) {
            DB::rollBack();
            return ServiceResponse::error($e->getMessage(), null, 500);
        }
    }

    public function uploadMediaForModel(string $modelType, int $modelId, UploadedFile $file, string $collection): ServiceResponse
    {
        $model = $this->getModel($modelType, $modelId);
        if (!$model) {
            return ServiceResponse::notFound('Model not found');
        }

        $media = null;
        DB::transaction(function () use ($model, $file, $collection, &$media) {
            $media = $model->addMedia($file)
                ->toMediaCollection($collection);

            if ($model instanceof \App\Models\Question) {
                $reference = MediaConfigHelper::createMediaReference(
                    'image', // Simplified type for now
                    $collection,
                    $media->id,
                    $media->getUrl()
                );
                $newConfig = MediaConfigHelper::addMediaReference($model->config ?? [], $reference);
                $this->questionRepository->update($model->id, ['config' => $newConfig]);
            }
        });

        return ServiceResponse::created($media);
    }

    public function deleteMedia(int $mediaId): ServiceResponse
    {
        DB::transaction(function () use ($mediaId) {
            $media = Media::find($mediaId);
            if (!$media) {
                throw new ModelNotFoundException('Media not found');
            }

            // Find all questions referencing this media and clean their config
            $questions = $this->questionRepository->findByJsonContains('config', 'media_id', $mediaId);

            foreach ($questions as $question) {
                $newConfig = MediaConfigHelper::removeMediaReference($question->config, $mediaId);
                $this->questionRepository->update($question->id, ['config' => $newConfig]);
            }
            
            $media->delete();
        });

        return ServiceResponse::noContent();
    }

    public function getQuestionMedia(int $questionId): ServiceResponse
    {
        try {
            $question = $this->questionRepository->find($questionId);
            if (!$question) {
                return ServiceResponse::notFound('Question not found');
            }

            $media = $question->getMedia('question-media');
            return ServiceResponse::success($media);

        } catch (\Exception $e) {
            return ServiceResponse::error($e->getMessage(), null, 500);
        }
    }

    public function getMedia(string $modelType, int $modelId, ?string $collection = null): ServiceResponse
    {
        try {
            $model = $this->getModel($modelType, $modelId);
            if (!$model) {
                return ServiceResponse::notFound('Model not found');
            }

            if ($collection) {
                $media = $model->getMedia($collection);
            } else {
                $media = $model->getMedia();
            }
            
            return ServiceResponse::success($media);

        } catch (\Exception $e) {
            return ServiceResponse::error($e->getMessage(), null, 500);
        }
    }

    public function updateMediaMetadata(int $mediaId, array $metadata): ServiceResponse
    {
        try {
            $media = Media::find($mediaId);
            if (!$media) {
                return ServiceResponse::notFound('Media not found');
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
            
            return ServiceResponse::success($media);

        } catch (\Exception $e) {
            return ServiceResponse::error($e->getMessage(), null, 500);
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

            return ServiceResponse::success($collections);

        } catch (\Exception $e) {
            return ServiceResponse::error($e->getMessage(), null, 500);
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