<?php

namespace App\Dtos;

use App\Core\BaseDto;

class ResponseDto extends BaseDto
{
    public function __construct(
        public readonly int $survey_id,
        public readonly ?int $user_id = null,
        public readonly ?array $metadata = null,
        public readonly ?int $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            survey_id: $data['survey_id'],
            user_id: $data['user_id'] ?? null,
            metadata: $data['metadata'] ?? null,
            id: $data['id'] ?? null,
        );
    }
} 