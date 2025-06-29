<?php

namespace App\Dtos;

use App\Core\BaseDto;

class LoginDto extends BaseDto
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}
} 