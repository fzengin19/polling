<?php

namespace App\Dtos;

use App\Core\BaseDto;

class RegisterDto extends BaseDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}
}