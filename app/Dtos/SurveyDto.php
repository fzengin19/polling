<?php

namespace App\Dtos;

use App\Core\BaseDto;

class SurveyDto extends BaseDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly ?array $settings = null,
        public readonly ?string $status = 'draft',
        public readonly ?int $created_by = null,
        public readonly ?int $template_id = null,
        public readonly ?int $template_version_id = null,
        public readonly ?string $expires_at = null,
        public readonly ?int $max_responses = null,
        public readonly ?int $id = null,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            settings: $data['settings'] ?? null,
            status: $data['status'] ?? 'draft',
            created_by: $data['created_by'] ?? null,
            template_id: $data['template_id'] ?? null,
            template_version_id: $data['template_version_id'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            max_responses: $data['max_responses'] ?? null,
            id: $data['id'] ?? null,
        );
    }
} 