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

/**
 * @group Authentication
 *
 * APIs for user authentication
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    /**
     * Register
     *
     * Register a new user and get an API token.
     * @response {
     *     "success": true,
     *     "message": "User registered successfully.",
     *     "data": {
     *         "user": {
     *             "id": 1,
     *             "name": "John Doe",
     *             "email": "john.doe@example.com"
     *         },
     *         "token": "{YOUR_API_TOKEN}"
     *     }
     * }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new RegisterDto(...$request->validated());
        $result = $this->authService->register($dto);
        return $result->toResponse();
    }

    /**
     * Login
     *
     * Log in a user and get an API token.
     * @response {
     *     "success": true,
     *     "message": "User logged in successfully.",
     *     "data": {
     *         "user": {
     *             "id": 1,
     *             "name": "John Doe",
     *             "email": "john.doe@example.com"
     *         },
     *         "token": "{YOUR_API_TOKEN}"
     *     }
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = new LoginDto(...$request->validated());
        $result = $this->authService->login($dto);
        return $result->toResponse();
    }

    /**
     * Logout
     *
     * Invalidate the current user's API token.
     * @authenticated
     * @response 200 {"success": true, "message": "Successfully logged out", "data": null}
     */
    public function logout(): JsonResponse
    {
        $result = $this->authService->logout();
        return $result->toResponse();
    }

    /**
     * Get Authenticated User
     *
     * Get the details of the currently authenticated user.
     * @authenticated
     * @responseFile storage/app/private/scribe/responses/auth.me.json
     */
    public function me(): JsonResponse
    {
        $result = $this->authService->getAuthenticatedUser();
        return $result->toResponse();
    }

    /**
     * Google Login
     *
     * Authenticate or register a user using a Google Access Token.
     * @response {
     *     "success": true,
     *     "message": "User authenticated successfully.",
     *     "data": {
     *         "user": {
     *             "id": 1,
     *             "name": "John Doe",
     *             "email": "john.doe@example.com"
     *         },
     *         "token": "{YOUR_API_TOKEN}"
     *     }
     * }
     */
    public function googleLogin(GoogleLoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginWithGoogle($request->validated('google_access_token'));
        return $result->toResponse();
    }
}
