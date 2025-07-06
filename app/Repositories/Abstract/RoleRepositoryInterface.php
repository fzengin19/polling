<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name): ?Role;
    public function assignRoleToModel(Model $model, string $roleName): void;
    public function removeRoleFromModel(Model $model, string $roleName): void;
} 