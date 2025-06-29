<?php

namespace App\Responses;

use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceResponse
{
    private $data;
    private $status;
    private ResourceMapInterface $resourceMap;

    public function __construct($data, ResourceMapInterface $resourceMap, int $status = 200)
    {
        $this->data = $data;
        $this->status = $status;
        $this->resourceMap = $resourceMap;
    }

    public function getData()
    {
        return $this->data;
    }

    public function toResponse()
    {
        $transformedData = $this->transformData($this->data);
        return response()->json($transformedData, $this->status);
    }

    private function transformData($data)
    {
        if (is_null($data)) return null;
        if (is_array($data)) return $this->transformArrayData($data);
        if ($data instanceof Collection || $data instanceof Model) return $this->transformModelData($data);
        return $data;
    }

    private function transformArrayData(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $result[$key] = null;
                continue;
            }

            if ($resourceClass = $this->getResourceClassForKey($key)) {
                $result[$key] = is_array($value) && isset($value[0])
                    ? $resourceClass::collection($value)
                    : new $resourceClass($value);
            } elseif (is_array($value)) {
                $result[$key] = $this->transformArrayData($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function transformModelData($data)
    {
        if (is_null($data)) return null;

        $modelName = Str::snake(class_basename($data));
        if ($resourceClass = $this->getResourceClassForKey($modelName)) {
            return $data instanceof Collection
                ? $resourceClass::collection($data)
                : new $resourceClass($data);
        }

        return $data;
    }

    private function getResourceClassForKey(string $key): ?string
    {
        return $this->resourceMap->resolve($key);
    }
}
