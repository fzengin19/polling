<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\UserRepositoryInterface;
use App\Services\Abstract\AuthServiceInterface;
use App\Dtos\RegisterDto;
use App\Dtos\LoginDto;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected ResourceMapInterface $resourceMap
    ) {}

    public function register(RegisterDto $dto): ServiceResponse
    {
        // Email unique kontrolü, servis katmanında yapılmalı
        $existingUser = $this->userRepository->findByEmail($dto->email);
        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => ['Email already taken.'],
            ]);
        }

        $user = $this->userRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return new ServiceResponse([
            'token' => $token,
            'user' => $user,
        ], $this->resourceMap, 201);
    }

    public function login(LoginDto $dto): ServiceResponse
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid Credentials.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return new ServiceResponse([
            'token' => $token,
            'user' => $user,
        ], $this->resourceMap);
    }

    public function logout(): ServiceResponse
    {
        auth()->user()?->currentAccessToken()?->delete();

        return new ServiceResponse(['message' => 'Logged out successfully.'], $this->resourceMap, 200);
    }

    public function getAuthenticatedUser(): ServiceResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            // Eğer user yoksa boş yanıt veya 401 dönebilirsin
            return new ServiceResponse(null, $this->resourceMap, 401);
        }

        $user = $this->userRepository->find($userId);

        return new ServiceResponse($user, $this->resourceMap);
    }
}
