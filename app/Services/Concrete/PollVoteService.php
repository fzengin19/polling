<?php

namespace App\Services;

use App\Repositories\Abstract\PollVoteRepositoryInterface;
use App\Models\PollVote;

class PollVoteService
{
    protected PollVoteRepositoryInterface $pollVoteRepository;

    public function __construct(PollVoteRepositoryInterface $pollVoteRepository)
    {
        $this->pollVoteRepository = $pollVoteRepository;
    }

    public function all()
    {
        return $this->pollVoteRepository->all();
    }

    public function find(int $id): ?PollVote
    {
        return $this->pollVoteRepository->find($id);
    }

    public function create(array $data): PollVote
    {
        return $this->pollVoteRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->pollVoteRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->pollVoteRepository->delete($id);
    }

    public function getByPoolId(int $poolId): ?PollVote
    {
        return $this->pollVoteRepository->getByPoolId($poolId);
    }
}
