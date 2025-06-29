<?php

namespace App\Services\Abstract;

use App\Dtos\QuestionDto;
use App\Models\Question;
use App\Models\SurveyPage;
use Illuminate\Support\Collection;
use App\Responses\ServiceResponse;

interface QuestionServiceInterface
{
    public function findQuestion(int $id): ServiceResponse;

    public function getQuestionsByPageId(int $pageId): ServiceResponse;

    public function createQuestion(QuestionDto $dto): ServiceResponse;

    public function updateQuestion(int $id, QuestionDto $dto): ServiceResponse;

    public function deleteQuestionById(int $id): ServiceResponse;

    public function reorder(int $pageId, array $questionIds): ServiceResponse;
}