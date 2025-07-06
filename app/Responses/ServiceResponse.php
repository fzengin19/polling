<?php

namespace App\Responses;

use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceResponse
{
    private bool $success;
    private $data;
    private ?string $message;
    private int $status;
    private ResourceMapInterface $resourceMap;

    public function __construct(bool $success, $data, ResourceMapInterface $resourceMap, string $message = '', int $status = 200)
    {
        $this->success = $success;
        $this->data = $data;
        $this->status = $status;
        $this->resourceMap = $resourceMap;
        $this->message = $message;
    }

    public static function success($data = null, string $message = 'Operation successful.', int $status = 200): self
    {
        return new self(true, $data, app(ResourceMapInterface::class), $message, $status);
    }

    public static function created($data = null, string $message = 'Resource created successfully.'): self
    {
        return new self(true, $data, app(ResourceMapInterface::class), $message, 201);
    }

    public static function noContent(string $message = 'Resource deleted successfully.'): self
    {
        return new self(true, null, app(ResourceMapInterface::class), $message, 204);
    }

    public static function error(string $message = 'An error occurred.', $data = null, int $status = 400): self
    {
        return new self(false, $data, app(ResourceMapInterface::class), $message, $status);
    }

    public static function notFound(string $message = 'Resource not found.'): self
    {
        return new self(false, null, app(ResourceMapInterface::class), $message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized.'): self
    {
        return new self(false, null, app(ResourceMapInterface::class), $message, 401);
    }

    public static function forbidden(string $message = 'Forbidden.'): self
    {
        return new self(false, null, app(ResourceMapInterface::class), $message, 403);
    }

    public function getData()
    {
        return $this->data;
    }

    public function toResponse()
    {
        if ($this->data instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
            return $this->toPaginatedResponse();
        }

        $responsePayload = [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->transformData($this->data),
        ];
        
        if (is_null($this->message) || $this->message === '') {
            if ($this->success) {
                $responsePayload['message'] = 'Operation successful.';
            } else {
                $responsePayload['message'] = 'An error occurred.';
            }
        }

        return response()->json($responsePayload, $this->status);
    }

    private function toPaginatedResponse()
    {
        $paginator = $this->data;
        $transformedData = $this->transformData($paginator->items());

        return response()->json([
            'success' => $this->success,
            'message' => $this->message ?: 'Operation successful.',
            'data' => $transformedData,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], $this->status);
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
