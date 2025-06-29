<?php

namespace App\Dtos;

use App\Core\BaseDto;

class RoleAssignmentDto extends BaseDto
{
    public readonly string $roleName;
    public readonly ?int $userId;
    public readonly ?int $surveyId;

    public function __construct(
        string $roleName,
        ?int $userId = null,
        ?int $surveyId = null
    ) {
        $this->roleName = $roleName;
        $this->userId = $userId;
        $this->surveyId = $surveyId;
    }

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            roleName: $data['role_name'],
            userId: $data['user_id'] ?? null,
            surveyId: $data['survey_id'] ?? null,
        );
    }
} 