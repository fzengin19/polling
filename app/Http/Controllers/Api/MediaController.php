<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Http\Requests\Media\UpdateMediaMetadataRequest;
use App\Dtos\MediaDto;
use App\Services\Abstract\MediaServiceInterface;
use Illuminate\Http\JsonResponse;

/**
 * @group Media Management
 *
 * APIs for managing media files (images, videos, documents)
 */
class MediaController extends Controller
{
    public function __construct(
        protected MediaServiceInterface $mediaService
    ) {}

    /**
     * Upload Media
     *
     * Upload a media file and associate it with a question.
     * @authenticated
     * @response 200 {"success": true, "message": "Media uploaded successfully", "data": {"id": 1, "file_name": "image.jpg", "file_path": "/storage/media/image.jpg"}}
     */
    public function upload(UploadMediaRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        
        $result = $this->mediaService->uploadMediaForModel(
            $validatedData['model_type'],
            $validatedData['model_id'],
            $request->file('file'),
            $validatedData['collection_name']
        );
        
        return $result->toResponse();
    }

    /**
     * Delete Media
     *
     * Delete a media file and its associated data.
     * @authenticated
     * @urlParam mediaId required The ID of the media file. Example: 1
     * @response 200 {"success": true, "message": "Media deleted successfully", "data": null}
     */
    public function destroy(int $mediaId): JsonResponse
    {
        $result = $this->mediaService->deleteMedia($mediaId);
        
        return $result->toResponse();
    }

    /**
     * Get Question Media
     *
     * Get all media files associated with a specific question.
     * @urlParam questionId required The ID of the question. Example: 1
     */
    public function getQuestionMedia(int $questionId): JsonResponse
    {
        $result = $this->mediaService->getQuestionMedia($questionId);
        
        return $result->toResponse();
    }

    /**
     * Update Media Metadata
     *
     * Update metadata (alt text, caption, etc.) for a media file.
     * @authenticated
     * @urlParam mediaId required The ID of the media file. Example: 1
     * @response 200 {"success": true, "message": "Media metadata updated successfully", "data": {"id": 1, "alt_text": "Updated alt text", "caption": "Updated caption"}}
     */
    public function updateMetadata(UpdateMediaMetadataRequest $request, int $mediaId): JsonResponse
    {
        $result = $this->mediaService->updateMediaMetadata($mediaId, $request->validated());
        
        return $result->toResponse();
    }
} 