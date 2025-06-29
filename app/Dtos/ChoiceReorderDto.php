<?php

namespace App\Dtos;

use App\Core\BaseDto;

class ChoiceReorderDto extends BaseDto
{
    public readonly int $id;
    public readonly int $orderIndex;

    public function __construct(int $id, int $orderIndex)
    {
        $this->id = $id;
        $this->orderIndex = $orderIndex;
    }

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            orderIndex: $data['order_index']
        );
    }
} 