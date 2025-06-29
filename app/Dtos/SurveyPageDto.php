<?php

namespace App\Dtos;

use App\Core\BaseDto;

class SurveyPageDto extends BaseDto
{
    public function __construct(
        public readonly int $survey_id,
        public readonly ?string $title = null,
        public readonly ?int $order_index = null,
        public readonly ?int $id = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            survey_id: $data['survey_id'],
            title: $data['title'] ?? null,
            order_index: $data['order_index'] ?? null,
            id: $data['id'] ?? null
        );
    }
} 