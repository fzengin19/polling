<?php

namespace App\Dtos;

use App\Core\BaseDto;

class SubmitResponseDto extends BaseDto
{
    public function __construct(
        public readonly int $responseId,
        public readonly array $answers, // array of AnswerDto
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            responseId: $data['response_id'],
            answers: $data['answers']
        );
    }
} 