<?php

namespace App\Dtos;

class AnswerDto
{
    public function __construct(
        public ?int $id,
        public int $response_id,
        public int $question_id,
        public ?int $choice_id,
        public ?string $value,
        public int $order_index,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            response_id: $data['response_id'],
            question_id: $data['question_id'],
            choice_id: $data['choice_id'] ?? null,
            value: $data['value'] ?? null,
            order_index: $data['order_index'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'response_id' => $this->response_id,
            'question_id' => $this->question_id,
            'choice_id' => $this->choice_id,
            'value' => $this->value,
            'order_index' => $this->order_index,
        ];
    }
} 