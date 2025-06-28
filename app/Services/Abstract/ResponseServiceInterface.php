<?php

namespace App\Services\Abstract;

use App\Dtos\ResponseDto;
use App\Dtos\SubmitResponseDto;
use App\Responses\ServiceResponse;

interface ResponseServiceInterface
{
    public function create(ResponseDto $dto): ServiceResponse;
    public function find(int $id): ServiceResponse;
    public function submit(SubmitResponseDto $dto): ServiceResponse;
    public function getStatistics(int $surveyId): ServiceResponse;
} 