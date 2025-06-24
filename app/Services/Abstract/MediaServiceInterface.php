<?php

namespace App\Services\Abstract;

use App\Dtos\MediaDto;
use App\Responses\ServiceResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface MediaServiceInterface
{
    public function uploadMedia(MediaDto $dto): ServiceResponse;
    public function uploadMediaForModel(Model $model, UploadedFile $file, string $collection): \Spatie\MediaLibrary\MediaCollections\Models\Media;
    public function deleteMedia(int $mediaId): ServiceResponse;
    public function getQuestionMedia(int $questionId): ServiceResponse;
    public function getMedia(Model $model, ?string $collection = null): \Illuminate\Support\Collection;
    public function updateMediaMetadata(int $mediaId, array $metadata): \Spatie\MediaLibrary\MediaCollections\Models\Media;
} 