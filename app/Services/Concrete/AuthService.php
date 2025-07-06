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
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

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

        return ServiceResponse::created([
            'token' => $token,
            'user' => $user,
        ], 'User registered successfully.');
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

        return ServiceResponse::success([
            'token' => $token,
            'user' => $user,
        ], 'Login successful.');
    }

    public function logout(): ServiceResponse
    {
        auth()->user()?->currentAccessToken()?->delete();

        return ServiceResponse::success(null, 'Logged out successfully.');
    }

    public function getAuthenticatedUser(): ServiceResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            // Eğer user yoksa boş yanıt veya 401 dönebilirsin
            return ServiceResponse::unauthorized('User not authenticated.');
        }

        $user = $this->userRepository->find($userId);

        return ServiceResponse::success($user);
    }

    /**
     * Google access token ile login/register akışı
     */
    public function loginWithGoogle(string $googleAccessToken): ServiceResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->userFromToken($googleAccessToken);

        // 1 email = 1 user prensibi: Önce email ile kullanıcıyı bul
        $user = $this->userRepository->findByEmail($googleUser->getEmail());

        if (!$user) {
            // Kullanıcı yoksa yeni kullanıcı oluştur
            $user = $this->userRepository->create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                // Google ile gelen kullanıcıya random bir şifre atanır, login için kullanılmaz
                'password' => Hash::make(Str::random(32)),
            ]);
        } else {
            // Kullanıcı varsa provider bilgilerini güncelle (eğer yoksa)
            if (!$user->provider) {
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return ServiceResponse::success([
            'token' => $token,
            'user' => $user,
        ], 'Google login successful.');
    }
}
