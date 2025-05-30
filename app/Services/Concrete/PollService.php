<?php

namespace App\Services\Concrete;

use App\Services\Abstract\PollServiceInterface;
use App\Repositories\Abstract\PollRepositoryInterface;
use App\Models\Poll;

class PollService implements PollServiceInterface
{
    protected PollRepositoryInterface $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    public function getAll()
    {
        return $this->pollRepository->all();
    }

    public function find(int $id): ?Poll
    {
        return $this->pollRepository->find($id);
    }

    public function findByUuid(string $uuid): ?Poll
    {
        return $this->pollRepository->findByUuid($uuid);
    }

    public function create(array $data): Poll
    {
        return $this->pollRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->pollRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->pollRepository->delete($id);
    }
}
