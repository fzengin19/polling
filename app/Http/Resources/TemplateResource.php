<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'created_by' => $this->created_by,
            'forked_from_template_id' => $this->forked_from_template_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->whenLoaded('creator', function () {
                return new UserResource($this->creator);
            }),
            'forked_from' => $this->whenLoaded('forkedFrom', function () {
                return new TemplateResource($this->forkedFrom);
            }),
            'forks_count' => $this->whenCounted('forks', function () {
                return $this->forks_count;
            }),
            'surveys_count' => $this->whenCounted('surveys', function () {
                return $this->surveys_count;
            }),
        ];
    }
} 