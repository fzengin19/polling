<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
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
            'status' => $this->status,
            'created_by' => $this->created_by,
            'template_id' => $this->template_id,
            'template_version_id' => $this->template_version_id,
            'settings' => $this->settings,
            'expires_at' => $this->expires_at,
            'max_responses' => $this->max_responses,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->whenLoaded('creator', function () {
                return new UserResource($this->creator);
            }),
            'template' => $this->whenLoaded('template', function () {
                return new TemplateResource($this->template);
            }),
            'template_version' => $this->whenLoaded('templateVersion', function () {
                return new TemplateVersionResource($this->templateVersion);
            }),
            'pages_count' => $this->whenCounted('pages', function () {
                return $this->pages_count;
            }),
            'responses_count' => $this->whenCounted('responses', function () {
                return $this->responses_count;
            }),
            'is_active' => $this->isActive(),
            'is_expired' => $this->isExpired(),
            'can_accept_responses' => $this->canAcceptResponses(),
        ];
    }
} 