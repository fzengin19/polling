<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\Abstract\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group User Management
 *
 * APIs for managing the authenticated user's profile.
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService
    ) {
    }

    /**
     * Update My Profile
     *
     * Update the profile information of the currently authenticated user.
     * @authenticated
     * @responseFile storage/app/private/scribe/responses/user.show.json
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $result = $this->userService->updateProfile($request->validated());
        return $result->toResponse();
    }
}
