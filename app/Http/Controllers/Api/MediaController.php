<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Dtos\MediaDto;
use App\Services\Abstract\MediaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function updateMetadata(int $mediaId, Request $request): JsonResponse
    {
        $request->validate([
            'alt_text' => 'sometimes|string|max:255',
            'caption' => 'sometimes|string|max:500',
        ]);

        $result = $this->mediaService->updateMediaMetadata($mediaId, $request->only(['alt_text', 'caption']));
        
        return $result->toResponse();
    }
} 