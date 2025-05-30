<?php

namespace App\Services\Abstract;

use App\Models\PollVote;

interface PollVoteServiceInterface
{
    public function all();

    public function find(int $id): ?PollVote;

    public function create(array $data): PollVote;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
    
    public function getByPoolId(int $id): bool;
}
