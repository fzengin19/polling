<?php

namespace App\Services\Abstract;

use App\Dtos\QuestionDto;
use App\Models\Question;
use App\Responses\ServiceResponse;
use Illuminate\Database\Eloquent\Collection;

interface QuestionServiceInterface
{
    public function getBySurveyPage(int $surveyPageId): ServiceResponse;
    public function getActiveBySurveyPage(int $surveyPageId): ServiceResponse;
    public function findById(int $id): ServiceResponse;
    public function create(QuestionDto $dto): ServiceResponse;
    public function update(int $id, QuestionDto $dto): ServiceResponse;
    public function delete(int $id): ServiceResponse;
    public function reorder(int $surveyPageId, array $questionIds): ServiceResponse;
    public function getByType(int $surveyPageId, string $type): ServiceResponse;
} 