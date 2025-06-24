<?php

namespace App\Dtos;

readonly class LoginDto {
    public function __construct(
        public string $email,
        public string $password
    ) {}
} 