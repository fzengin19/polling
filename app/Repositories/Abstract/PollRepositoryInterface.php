<?php

namespace App\Repositories\Abstract;

use App\Core\BaseRepositoryInterface;
use App\Models\Poll;

interface PollRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUuid(string $uuid): ?Poll;
}
