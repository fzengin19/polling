<?php

namespace App\Dtos;

use App\Core\BaseDto;

class AnswerDto extends BaseDto
{
    public function __construct(
        public readonly int $response_id,
        public readonly int $question_id,
        public readonly ?int $choice_id = null,
        public readonly ?string $value = null,
        public readonly ?int $order_index = 0,
        public readonly ?int $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            response_id: $data['response_id'],
            question_id: $data['question_id'],
            choice_id: $data['choice_id'] ?? null,
            value: $data['value'] ?? null,
            order_index: $data['order_index'] ?? 0,
            id: $data['id'] ?? null,
        );
    }
} 