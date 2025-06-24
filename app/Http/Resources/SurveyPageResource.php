<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'survey_id' => $this->survey_id,
            'order_index' => $this->order_index,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'survey' => $this->whenLoaded('survey', function () {
                return new SurveyResource($this->survey);
            }),
            'questions_count' => $this->whenCounted('questions', function () {
                return $this->questions_count;
            }),
            'next_page' => $this->when($this->relationLoaded('survey'), function () {
                return $this->getNextPage() ? $this->getNextPage()->id : null;
            }),
            'previous_page' => $this->when($this->relationLoaded('survey'), function () {
                return $this->getPreviousPage() ? $this->getPreviousPage()->id : null;
            }),
        ];
    }
} 