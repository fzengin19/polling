<?php

namespace App\Services\Abstract;

use App\Dtos\ChoiceDto;
use App\Responses\ServiceResponse;

interface ChoiceServiceInterface
{
    public function create(ChoiceDto $dto): ServiceResponse;
    public function update(int $id, ChoiceDto $dto): ServiceResponse;
    public function delete(int $id): ServiceResponse;
    public function getByQuestion(int $questionId): ServiceResponse;
    public function reorder(int $questionId, array $choices): ServiceResponse;
} 