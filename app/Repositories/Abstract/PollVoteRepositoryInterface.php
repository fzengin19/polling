<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Models\PollVote;

interface PollVoteRepositoryInterface extends BaseRepositoryInterface
{
    public function getByPoolId(int $id, array $with = []);
}
