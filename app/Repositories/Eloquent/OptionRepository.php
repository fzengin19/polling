<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Abstract\OptionRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\Option;

class OptionRepository extends BaseRepository implements OptionRepositoryInterface
{
    public function __construct(Option $model)
    {
        $this->model = $model;
    }

}
