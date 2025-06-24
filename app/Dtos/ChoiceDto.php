<?php

namespace App\Dtos;

class ChoiceDto
{
    public function __construct(
        public ?int $id,
        public int $question_id,
        public string $label,
        public string $value,
        public int $order_index,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            question_id: $data['question_id'],
            label: $data['label'],
            value: $data['value'],
            order_index: $data['order_index'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'label' => $this->label,
            'value' => $this->value,
            'order_index' => $this->order_index,
        ];
    }
} 