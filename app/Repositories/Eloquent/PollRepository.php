<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Abstract\PollRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Poll;

class PollRepository extends BaseRepository implements PollRepositoryInterface
{
    public function __construct(Poll $model)
    {
        $this->model = $model;
    }

    public function findByUuid(string $uuid, array $with = []): ?Poll
    {
        return $this->model->where('uuid', $uuid)->with($with)->first();
    }
}
