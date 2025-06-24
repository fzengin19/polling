<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}

