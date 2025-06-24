<?php

namespace App\Services\Abstract;

use App\Dtos\SurveyPageDto;
use App\Models\SurveyPage;
use App\Responses\ServiceResponse;

interface SurveyPageServiceInterface
{
    public function create(SurveyPageDto $dto): ServiceResponse;
    public function update(int $id, SurveyPageDto $dto): ServiceResponse;
    public function delete(int $id): ServiceResponse;
    public function find(int $id): ServiceResponse;
    public function getBySurvey(int $surveyId): ServiceResponse;
    public function getOrderedPages(int $surveyId): ServiceResponse;
    public function reorder(int $surveyId, array $pageIds): ServiceResponse;
} 