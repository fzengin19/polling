<?php

namespace App\Services\Abstract;

use App\Dtos\MediaDto;
use App\Responses\ServiceResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface MediaServiceInterface
{
    public function uploadMedia(MediaDto $dto): ServiceResponse;
    public function uploadMediaForModel(string $modelType, int $modelId, UploadedFile $file, string $collection): ServiceResponse;
    public function deleteMedia(int $mediaId): ServiceResponse;
    public function getQuestionMedia(int $questionId): ServiceResponse;
    public function getMedia(string $modelType, int $modelId, ?string $collection = null): ServiceResponse;
    public function updateMediaMetadata(int $mediaId, array $metadata): ServiceResponse;
    public function getCollections(string $modelType): ServiceResponse;
} 