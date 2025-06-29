<?php

namespace App\Services\Abstract;

use App\Dtos\SurveyPageDto;
use App\Models\SurveyPage;
use Illuminate\Support\Collection;
use App\Responses\ServiceResponse;

interface SurveyPageServiceInterface
{
    public function getPagesBySurveyId(int $surveyId): ServiceResponse;
    
    public function createPage(SurveyPageDto $dto): ServiceResponse;
    
    public function findPage(int $id): ServiceResponse;
    
    public function updatePage(int $id, array $data): ServiceResponse;

    public function deletePage(int $id): ServiceResponse;
    
    public function reorderPages(int $surveyId, array $pageIds): ServiceResponse;
}