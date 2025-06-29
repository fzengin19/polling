<?php

namespace App\Dtos;

use App\Core\BaseDto;

class TemplateDto extends BaseDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly bool $is_public = false,
        public readonly ?int $created_by = null,
        public readonly ?int $forked_from_template_id = null,
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
            is_public: $data['is_public'] ?? false,
            created_by: $data['created_by'] ?? null,
            forked_from_template_id: $data['forked_from_template_id'] ?? null,
            id: $data['id'] ?? null,
        );
    }
} 