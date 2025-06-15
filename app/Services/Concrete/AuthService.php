<?php

namespace App\Services\Concrete;

use App\Repositories\Abstract\UserRepositoryInterface;
use App\Models\Option;
use App\Services\Abstract\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function authenticate() {
        
    }
}
