<?php

namespace App\Dtos;

class SurveyPageDto
{
    public function __construct(
        public readonly ?int $surveyId,
        public readonly ?string $title,
        public readonly ?int $orderIndex = null
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            surveyId: $data['survey_id'] ?? null,
            title: $data['title'] ?? null,
            orderIndex: $data['order_index'] ?? null
        );
    }

    /**
     * Convert to array for database operations
     */
    public function toArray(): array
    {
        $array = [
            'title' => $this->title,
            'order_index' => $this->orderIndex,
        ];
        
        if ($this->surveyId !== null) {
            $array['survey_id'] = $this->surveyId;
        }
        
        return $array;
    }
} 