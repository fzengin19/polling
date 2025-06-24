<?php

namespace App\Services\Abstract;

use App\Dtos\MediaDto;
use App\Responses\ServiceResponse;

interface MediaServiceInterface
{
    public function uploadMedia(MediaDto $dto): ServiceResponse;
    public function deleteMedia(int $mediaId): ServiceResponse;
    public function getQuestionMedia(int $questionId): ServiceResponse;
    public function updateMediaMetadata(int $mediaId, array $metadata): ServiceResponse;
} 