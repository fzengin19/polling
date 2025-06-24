<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TemplateVersionResource extends JsonResource
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
            'template_id' => $this->template_id,
            'version' => $this->version,
            'snapshot' => $this->snapshot,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'template' => $this->whenLoaded('template', function () {
                return new TemplateResource($this->template);
            }),
        ];
    }
} 