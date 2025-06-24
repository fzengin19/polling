<?php

namespace App\Services\Concrete;

use App\Dtos\MediaDto;
use App\Models\Question;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use App\Responses\Concrete\ApiResourceMap;
use App\Services\Abstract\MediaServiceInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MediaService implements MediaServiceInterface
{
    public function __construct(
        protected ApiResourceMap $resourceMap
    ) {}

    public function uploadMedia(MediaDto $dto): ServiceResponse
    {
        try {
            DB::beginTransaction();

            $question = Question::find($dto->question_id);
            if (!$question) {
                return new ServiceResponse(['error' => 'Question not found'], $this->resourceMap, 404);
            }

            // MediaLibrary will handle the file upload and storage
            $media = $question->addMediaFromRequest('file')
                ->withCustomProperties([
                    'alt_text' => $dto->alt_text,
                    'caption' => $dto->caption,
                ])
                ->toMediaCollection('question-media');

            DB::commit();
            return new ServiceResponse(['media' => $media], $this->resourceMap, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function deleteMedia(int $mediaId): ServiceResponse
    {
        try {
            $media = Media::find($mediaId);
            if (!$media) {
                return new ServiceResponse(['error' => 'Media not found'], $this->resourceMap, 404);
            }

            $media->delete();
            return new ServiceResponse(['message' => 'Media deleted successfully'], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function getQuestionMedia(int $questionId): ServiceResponse
    {
        try {
            $question = Question::find($questionId);
            if (!$question) {
                return new ServiceResponse(['error' => 'Question not found'], $this->resourceMap, 404);
            }

            $media = $question->getMedia('question-media');
            return new ServiceResponse(['media' => $media], $this->resourceMap, 200);

        } catch (\Exception $e) {
            return new ServiceResponse(['error' => $e->getMessage()], $this->resourceMap, 500);
        }
    }

    public function updateMediaMetadata(int $mediaId, array $metadata): \Spatie\MediaLibrary\MediaCollections\Models\Media
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
        
        \Illuminate\Support\Facades\Log::info('Updating media metadata', [
            'media_id' => $mediaId,
            'incoming_metadata' => $metadata,
            'display_order_value' => $metadata['display_order'] ?? 'not_set',
            'display_order_type' => gettype($metadata['display_order'] ?? null)
        ]);
        
        $media->setCustomProperty('alt_text', $metadata['alt_text'] ?? null);
        $media->setCustomProperty('caption', $metadata['caption'] ?? null);
        $displayOrder = isset($metadata['display_order']) ? (int)$metadata['display_order'] : 0;
        $media->setCustomProperty('display_order', $displayOrder);
        
        \Illuminate\Support\Facades\Log::info('Setting display_order', [
            'display_order' => $displayOrder,
            'display_order_type' => gettype($displayOrder)
        ]);
        
        $media->save();
        $media->refresh();
        
        \Illuminate\Support\Facades\Log::info('After save and refresh', [
            'custom_properties' => $media->custom_properties,
            'display_order_from_properties' => $media->getCustomProperty('display_order')
        ]);
        
        return $media;
    }

    public function uploadMediaForModel(Model $model, UploadedFile $file, string $collection): \Spatie\MediaLibrary\MediaCollections\Models\Media
    {
        return $model->addMedia($file)
            ->toMediaCollection($collection);
    }

    public function getMedia(Model $model, ?string $collection = null): \Illuminate\Support\Collection
    {
        if ($collection) {
            return $model->getMedia($collection);
        }
        
        return $model->getMedia();
    }
} 