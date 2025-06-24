<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Abstract\UserRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

}
