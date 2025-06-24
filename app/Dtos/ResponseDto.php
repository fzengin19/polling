<?php

namespace App\Dtos;

class ResponseDto
{
    public function __construct(
        public ?int $id,
        public int $survey_id,
        public ?int $user_id,
        public ?string $started_at,
        public ?string $submitted_at,
        public ?array $metadata,
        public bool $is_complete,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            survey_id: $data['survey_id'],
            user_id: $data['user_id'] ?? null,
            started_at: $data['started_at'] ?? null,
            submitted_at: $data['submitted_at'] ?? null,
            metadata: $data['metadata'] ?? null,
            is_complete: $data['is_complete'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'survey_id' => $this->survey_id,
            'user_id' => $this->user_id,
            'started_at' => $this->started_at,
            'submitted_at' => $this->submitted_at,
            'metadata' => $this->metadata,
            'is_complete' => $this->is_complete,
        ];
    }
} 