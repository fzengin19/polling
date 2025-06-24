<?php

namespace App\Dtos;

class SurveyDto
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $status,
        public readonly int $createdBy,
        public readonly ?int $templateId = null,
        public readonly ?int $templateVersionId = null,
        public readonly ?array $settings = null,
        public readonly ?string $expiresAt = null,
        public readonly ?int $maxResponses = null
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'draft',
            createdBy: $data['created_by'],
            templateId: $data['template_id'] ?? null,
            templateVersionId: $data['template_version_id'] ?? null,
            settings: $data['settings'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            maxResponses: $data['max_responses'] ?? null
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
            'status' => $this->status,
            'created_by' => $this->createdBy,
            'template_id' => $this->templateId,
            'template_version_id' => $this->templateVersionId,
            'settings' => $this->settings,
            'expires_at' => $this->expiresAt,
            'max_responses' => $this->maxResponses,
        ];
    }
} 