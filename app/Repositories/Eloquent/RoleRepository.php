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

    public function assignRoleToModel(\Illuminate\Database\Eloquent\Model $model, string $roleName): void
    {
        $model->assignRole($roleName);
    }

    public function removeRoleFromModel(\Illuminate\Database\Eloquent\Model $model, string $roleName): void
    {
        $model->removeRole($roleName);
    }
} 