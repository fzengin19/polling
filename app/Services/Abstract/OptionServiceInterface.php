<?php

namespace App\Services\Abstract;

use App\Models\Option;

interface OptionServiceInterface
{
    public function getAll();

    public function find(int $id): ?Option;

    public function create(array $data): Option;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
