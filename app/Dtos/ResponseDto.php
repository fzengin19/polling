<?php

namespace App\Dtos;

use App\Core\BaseDto;

class ResponseDto extends BaseDto
{
    public function __construct(
        public readonly int $surveyId,
        public readonly ?array $metadata = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            surveyId: $data['survey_id'],
            metadata: $data['metadata'] ?? null,
        );
    }
} 