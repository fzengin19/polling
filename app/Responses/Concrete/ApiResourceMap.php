<?php 

namespace App\Responses\Concrete;

use App\Http\Resources\PollResource;
use App\Http\Resources\UserResource;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Str;

class ApiResourceMap implements ResourceMapInterface
{
    private array $map = [
        'user' => UserResource::class,
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
