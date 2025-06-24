<?php

namespace App\Services\Abstract;

use App\Dtos\SurveyDto;
use App\Models\Survey;
use App\Responses\ServiceResponse;

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
} 