<?php

namespace App\Services\Abstract;

use App\Dtos\SurveyDto;
use App\Models\Survey;
use App\Responses\ServiceResponse;
use App\Dtos\SurveyPageDto;

interface SurveyServiceInterface
{
    public function create(SurveyDto $dto): ServiceResponse;
    public function update(int $id, SurveyDto $dto): ServiceResponse;
    public function delete(int $id): ServiceResponse;
    public function find(int $id): ServiceResponse;
    public function getAll(): ServiceResponse;
    public function getByUser(int $userId): ServiceResponse;
    public function getByStatus(string $status): ServiceResponse;
    public function getActiveSurveys(): ServiceResponse;
    public function getByTemplate(int $templateId): ServiceResponse;
    public function publish(int $id): ServiceResponse;
    public function archive(int $id): ServiceResponse;
    public function duplicate(int $id): ServiceResponse;
    
    // Page Management
    public function getOrderedPages(int $surveyId): ServiceResponse;
    public function createPage(SurveyPageDto $dto): ServiceResponse;
    public function findPage(int $id): ServiceResponse;
    public function updatePage(int $id, SurveyPageDto $dto): ServiceResponse;
    public function deletePage(int $id): ServiceResponse;
    public function reorderPages(int $surveyId, array $pageIds): ServiceResponse;
} 