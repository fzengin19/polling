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

    public function getByPoolId(int $poolId, array $with = [])
    {
        return $this->model->where('pool_id', $poolId)->with($with)->get();
    }

   public function getByQuestionId(int $questionId, array $with = [])
    {
        return $this->model->where('question_id', $questionId)->with($with)->get();
    }

}
