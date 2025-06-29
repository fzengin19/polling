<?php

namespace App\Dtos;

use App\Core\BaseDto;

class QuestionDto extends BaseDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $type = null,
        public readonly ?int $page_id = null,
        public readonly ?string $help_text = null,
        public readonly ?string $placeholder = null,
        public readonly bool $is_required = false,
        public readonly ?array $config = null,
        public readonly ?int $order_index = null,
        public readonly ?int $id = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            type: $data['type'] ?? null,
            page_id: $data['page_id'] ?? null,
            help_text: $data['help_text'] ?? null,
            placeholder: $data['placeholder'] ?? null,
            is_required: $data['is_required'] ?? false,
            config: $data['config'] ?? null,
            order_index: $data['order_index'] ?? null,
            id: $data['id'] ?? null
        );
    }
} 