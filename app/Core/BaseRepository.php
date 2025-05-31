<?php

namespace App\Core;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function all(array $with = [])
    {
        return $this->model->with($with)->get();
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->model->with($with)->paginate($perPage);
    }

    public function find(int $id, array $with = [])
    {
        return $this->model->with($with)->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $record = $this->find($id);
        return $record ? $record->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);
        return $record ? $record->delete() : false;
    }
}