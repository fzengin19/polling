<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function find($id);
    public function findBy(string $field, $value): Collection;
    public function all(): Collection;
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id): bool;
    public function paginate(int $perPage = 15, array $with = []);
}
