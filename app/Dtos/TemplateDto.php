<?php

namespace App\Dtos;

class TemplateDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly bool $isPublic,
        public readonly int $createdBy,
        public readonly ?int $forkedFromTemplateId = null
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            isPublic: $data['is_public'] ?? false,
            createdBy: $data['created_by'],
            forkedFromTemplateId: $data['forked_from_template_id'] ?? null
        );
    }

    /**
     * Convert to array for database operations
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'is_public' => $this->isPublic,
            'created_by' => $this->createdBy,
            'forked_from_template_id' => $this->forkedFromTemplateId,
        ];
    }
} 