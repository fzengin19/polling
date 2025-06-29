<?php

namespace App\Core;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;

abstract class BaseDto implements Arrayable
{
    /**
     * Get the instance as an array, excluding null values.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
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

    /**
     * Get the instance as an array formatted for database operations (snake_case keys).
     *
     * @return array
     */
    public function toDatabaseArray(): array
    {
        $data = [];
        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $propertyName = $reflectionProperty->getName();

            if ($reflectionProperty->isInitialized($this)) {
                $value = $this->{$propertyName};
                if ($value !== null) {
                    $data[Str::snake($propertyName)] = $value;
                }
            }
        }

        return $data;
    }
} 