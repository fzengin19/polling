<?php

namespace App\Core;

interface BaseRepositoryInterface
{
    public function all();

    public function paginate(int $perPage = 15);

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
