<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Repositories\Abstract\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->where('name', $name)->first();
    }
} 