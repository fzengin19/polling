<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Models\Response;

interface ResponseRepositoryInterface extends BaseRepositoryInterface
{
    public function getCountBySurvey(int $surveyId): int;
    public function getCompletedCountBySurvey(int $surveyId): int;
    public function getStatistics(int $surveyId): array;
    public function countBySurvey(int $surveyId): int;
} 