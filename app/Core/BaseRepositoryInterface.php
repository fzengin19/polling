<?php

namespace App\Core;

interface BaseRepositoryInterface
{
    public function all(array $with = []);

    public function paginate(int $perPage = 15, array $with = []);

    public function find(int $id, array $with = []);

    public function create(array $data);

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
