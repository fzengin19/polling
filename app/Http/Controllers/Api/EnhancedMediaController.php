<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\EnhancedUploadMediaRequest;
use App\Http\Requests\Media\UpdateMediaMetadataRequest;
use App\Services\Abstract\MediaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnhancedMediaController extends Controller
{
    public function __construct(
        private MediaServiceInterface $mediaService
    ) {}

    /**
     * Upload media for a specific model
     */
    public function uploadMedia(EnhancedUploadMediaRequest $request, string $modelType, int $modelId): JsonResponse
    {
        $collection = $request->input('collection', 'default');
        $file = $request->file('file');

        $result = $this->mediaService->uploadMediaForModel($modelType, $modelId, $file, $collection);
        return $result->toResponse();
    }

    /**
     * Get media for a specific model
     */
    public function getMedia(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $collection = $request->input('collection');
        
        $result = $this->mediaService->getMedia($modelType, $modelId, $collection);
        return $result->toResponse();
    }

    /**
     * Update media metadata
     */
    public function updateMediaMetadata(UpdateMediaMetadataRequest $request, int $mediaId): JsonResponse
    {
        $result = $this->mediaService->updateMediaMetadata($mediaId, $request->validated());
        return $result->toResponse();
    }

    /**
     * Delete media
     */
    public function deleteMedia(int $mediaId): JsonResponse
    {
        $result = $this->mediaService->deleteMedia($mediaId);
        return $result->toResponse();
    }

    /**
     * Get available collections for a model type
     */
    public function getCollections(string $modelType): JsonResponse
    {
        $result = $this->mediaService->getCollections($modelType);
        return $result->toResponse();
    }
} 