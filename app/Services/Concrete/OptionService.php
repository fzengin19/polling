<?php

namespace App\Services\Concrete;

use App\Services\Abstract\OptionServiceInterface;
use App\Repositories\Abstract\OptionRepositoryInterface;
use App\Models\Option;

class OptionService implements OptionServiceInterface
{
    protected OptionRepositoryInterface $optionRepository;

    public function __construct(OptionRepositoryInterface $optionRepository)
    {
        $this->optionRepository = $optionRepository;
    }

    public function getAll()
    {
        return $this->optionRepository->all();
    }

    public function find(int $id): ?Option
    {
        return $this->optionRepository->find($id);
    }

    public function create(array $data): Option
    {
        return $this->optionRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->optionRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->optionRepository->delete($id);
    }
}
