<?php

namespace App\Dtos;

readonly class RegisterDto {
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}