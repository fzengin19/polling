<?php

namespace App\Services\Abstract;

use App\Dtos\ChoiceDto;
use App\Models\Choice;
use App\Models\Question;
use App\Responses\ServiceResponse;
use Illuminate\Support\Collection;

interface ChoiceServiceInterface
{
    public function getByQuestion(int $questionId): ServiceResponse;
    
    public function createChoice(ChoiceDto $dto): ServiceResponse;

    public function updateChoice(int $id, ChoiceDto $dto): ServiceResponse;

    public function deleteChoiceById(int $id): ServiceResponse;
    
    public function reorder(int $questionId, array $choices): ServiceResponse;

    public function findChoice(int $id): ServiceResponse;
} 