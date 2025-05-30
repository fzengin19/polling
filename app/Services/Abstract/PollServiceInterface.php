<?php 
namespace App\Services\Abstract;

use App\Models\Poll;

interface PollServiceInterface
{
    public function getAll();

    public function find(int $id): ?Poll;

    public function findByUuid(string $uuid): ?Poll;

    public function create(array $data): Poll;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
