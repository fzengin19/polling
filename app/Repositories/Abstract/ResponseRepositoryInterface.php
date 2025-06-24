<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Dtos\ResponseDto;

interface ResponseRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySurvey(int $surveyId): array;
    public function findByUser(int $userId): array;
    public function findBySurveyAndUser(int $surveyId, int $userId): ?ResponseDto;
    public function getSurveyResponseCount(int $surveyId): int;
} 