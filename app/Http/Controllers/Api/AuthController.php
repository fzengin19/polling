<?php

namespace App\Http\Controllers\Api;

use App\Services\Abstract\AuthServiceInterface;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Dtos\RegisterDto;
use App\Dtos\LoginDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new RegisterDto(...$request->validated());
        $result = $this->authService->register($dto);
        return $result->toResponse();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = new LoginDto(...$request->validated());
        $result = $this->authService->login($dto);
        return $result->toResponse();
    }

    public function logout(): JsonResponse
    {
        $result = $this->authService->logout();
        return $result->toResponse();
    }

    public function me(): JsonResponse
    {
        $result = $this->authService->getAuthenticatedUser();
        return $result->toResponse();
    }

    /**
     * Google access token ile login/register
     */
    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginWithGoogle($request->validated('google_access_token'));
        return $result->toResponse();
    }
}
