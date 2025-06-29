<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use Spatie\Permission\Models\Role;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name): ?Role;
} 