<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;

interface ResponseRepositoryInterface extends BaseRepositoryInterface
{
    public function getCountBySurvey(int $surveyId): int;
    public function getCompletedCountBySurvey(int $surveyId): int;
} 