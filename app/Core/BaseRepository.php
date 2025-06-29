<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findBy(string $field, $value): Collection
    {
        return $this->model->where($field, $value)->get();
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        return $record ? $record->update($data) : false;
    }

    public function delete($id): bool
    {
        $record = $this->find($id);
        return $record ? $record->delete() : false;
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->model->with($with)->paginate($perPage);
    }
}