<?php

namespace App\Dtos;

class SubmitResponseDto
{
    public function __construct(
        public int $responseId,
        public array $answers,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            responseId: $data['response_id'],
            answers: $data['answers'],
        );
    }

    public function toArray(): array
    {
        return [
            'response_id' => $this->responseId,
            'answers' => $this->answers,
        ];
    }
} 