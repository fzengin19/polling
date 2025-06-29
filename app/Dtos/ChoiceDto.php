<?php

namespace App\Dtos;

use App\Core\BaseDto;

class ChoiceDto extends BaseDto
{
    public function __construct(
        public readonly string $label,
        public readonly string $value,
        public readonly ?int $question_id = null,
        public readonly ?int $order_index = null,
        public readonly ?int $id = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'],
            value: $data['value'],
            question_id: $data['question_id'] ?? null,
            order_index: $data['order_index'] ?? null,
            id: $data['id'] ?? null
        );
    }
} 