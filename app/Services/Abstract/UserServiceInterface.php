<?php

namespace App\Services\Abstract;

use App\Responses\ServiceResponse;
 
interface UserServiceInterface
{
    public function updateProfile(array $data): ServiceResponse;
} 