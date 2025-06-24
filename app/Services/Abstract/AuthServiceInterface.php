<?php

namespace App\Services\Abstract;

use App\Dtos\RegisterDto;
use App\Dtos\LoginDto;
use App\Models\User;
use App\Responses\ServiceResponse;

interface AuthServiceInterface
{
    public function register(RegisterDto $dto): ServiceResponse;
    public function login(LoginDto $dto): ServiceResponse;
    public function logout(): ServiceResponse;
    public function getAuthenticatedUser(): ServiceResponse;
    public function loginWithGoogle(string $googleAccessToken): ServiceResponse;
}
