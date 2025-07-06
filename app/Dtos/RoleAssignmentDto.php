<?php

namespace App\Dtos;

use App\Core\BaseDto;

class RoleAssignmentDto extends BaseDto
{
    public function __construct(
        public readonly string $roleName,
        public readonly string $modelType,
        public readonly int $modelId
    ) {
    }

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            roleName: $data['role_name'],
            modelType: $data['model_type'],
            modelId: $data['model_id'],
        );
    }
} 