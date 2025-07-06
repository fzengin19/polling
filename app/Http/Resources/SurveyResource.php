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
        $settings = is_array($this->settings) ? $this->settings : (json_decode($this->settings, true) ?? []);
        
        if (isset($settings['theme']) && is_array($settings['theme'])) {
            $settings['theme']['logo_url'] = $this->getFirstMediaUrl('survey-logos');
            unset($settings['theme']['logo_media_id']);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'template_id' => $this->template_id,
            'settings' => $settings,
            'expires_at' => $this->expires_at,
            'max_responses' => $this->max_responses,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pages' => SurveyPageResource::collection($this->whenLoaded('pages')),
            'template' => new TemplateResource($this->whenLoaded('template')),
        ];
    }
} 