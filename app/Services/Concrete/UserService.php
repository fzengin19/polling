<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\UserRepositoryInterface;
use App\Services\Abstract\UserServiceInterface;
use App\Responses\ServiceResponse;
use App\Responses\Abstract\ResourceMapInterface;
use Illuminate\Support\Facades\Auth;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ResourceMapInterface $resourceMap
    ) {}

    public function updateProfile(array $data): ServiceResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return ServiceResponse::unauthorized('User not authenticated.');
        }

        $this->userRepository->update($user->id, $data);
        $updatedUser = $this->userRepository->find($user->id);
        
        if (!$updatedUser) {
            return ServiceResponse::error('Failed to update profile.');
        }

        return ServiceResponse::success($updatedUser, 'Profile updated successfully.');
    }
} 