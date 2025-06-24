<?php 

namespace App\Responses\Concrete;

use App\Http\Resources\PollResource;
use App\Http\Resources\TemplateResource;
use App\Http\Resources\TemplateVersionResource;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Str;

class ApiResourceMap implements ResourceMapInterface
{
    private array $map = [
        'poll' => PollResource::class,
        'template' => TemplateResource::class,
        'templates' => TemplateResource::class,
        'template_version' => TemplateVersionResource::class,
        'template_versions' => TemplateVersionResource::class,
    ];

    public function resolve(string $key): ?string
    {
        $singular = Str::singular($key);
        $plural = Str::plural($key);

        return $this->map[$key]
            ?? $this->map[$singular]
            ?? $this->map[$plural]
            ?? null;
    }
}
