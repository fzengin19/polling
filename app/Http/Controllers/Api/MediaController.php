<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Http\Requests\Media\UpdateMediaMetadataRequest;
use App\Dtos\MediaDto;
use App\Services\Abstract\MediaServiceInterface;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    public function __construct(
        protected MediaServiceInterface $mediaService
    ) {}

    public function upload(UploadMediaRequest $request): JsonResponse
    {
        $dto = MediaDto::fromArray($request->validated());
        $result = $this->mediaService->uploadMedia($dto);
        
        return $result->toResponse();
    }

    public function delete(int $mediaId): JsonResponse
    {
        $result = $this->mediaService->deleteMedia($mediaId);
        
        return $result->toResponse();
    }

    public function getQuestionMedia(int $questionId): JsonResponse
    {
        $result = $this->mediaService->getQuestionMedia($questionId);
        
        return $result->toResponse();
    }

    public function updateMetadata(int $mediaId, UpdateMediaMetadataRequest $request): JsonResponse
    {
        $result = $this->mediaService->updateMediaMetadata($mediaId, $request->validated());
        
        return $result->toResponse();
    }
} 