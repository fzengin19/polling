<?php

namespace App\Responses\Abstract;

interface ResourceMapInterface
{
    public function resolve(string $key): ?string;
}
