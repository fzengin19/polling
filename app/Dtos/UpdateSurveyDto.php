<?php

namespace App\Dtos;

use App\Core\BaseDto;

class UpdateSurveyDto extends BaseDto
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?array $settings = null,
        public readonly ?string $status = null,
        public readonly ?string $expires_at = null,
        public readonly ?int $max_responses = null,
    ) {}

    /**
     * Create DTO from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            settings: $data['settings'] ?? null,
            status: $data['status'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            max_responses: $data['max_responses'] ?? null,
        );
    }

    /**
     * Get the instance as an array, including only non-null values for update operations.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        $class = new \ReflectionClass(static::class);

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();

            if ($reflectionProperty->isInitialized($this)) {
                $value = $this->{$propertyName};
                if ($value !== null) {
                    $data[$propertyName] = $value;
                }
            }
        }

        return $data;
    }
} 