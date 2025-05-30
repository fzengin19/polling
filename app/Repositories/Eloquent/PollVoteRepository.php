<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Abstract\PollVoteRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\PollVote;

class PollVoteRepository extends BaseRepository implements PollVoteRepositoryInterface
{
    public function __construct(PollVote $model)
    {
        $this->model = $model;
    }

}
