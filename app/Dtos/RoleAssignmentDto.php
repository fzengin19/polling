<?php

namespace App\Dtos;

class RoleAssignmentDto
{
    public function __construct(
        public readonly string $roleName,
        public readonly ?int $userId = null,
        public readonly ?int $surveyId = null
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            roleName: $data['role_name'],
            userId: $data['user_id'] ?? null,
            surveyId: $data['survey_id'] ?? null
        );
    }

    /**
     * Convert to array for database operations
     */
    public function toArray(): array
    {
        return [
            'role_name' => $this->roleName,
            'user_id' => $this->userId,
            'survey_id' => $this->surveyId,
        ];
    }
} 