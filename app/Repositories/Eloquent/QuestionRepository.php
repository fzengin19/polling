<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\Option;
use App\Repositories\Abstract\QuestionRepositoryInterface;

class QuestionRepository extends BaseRepository implements QuestionRepositoryInterface
{
    public function __construct(Option $model)
    {
        $this->model = $model;
    }

    public function getByPoolId(int $poolId, array $with = [])
    {
        return $this->model->where('pool_id', $poolId)->with($with)->get();
    }
}
