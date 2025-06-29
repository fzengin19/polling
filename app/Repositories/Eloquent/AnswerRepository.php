<?php

namespace App\Repositories\Eloquent;

use App\Core\BaseRepository;
use App\Models\Answer;
use App\Repositories\Abstract\AnswerRepositoryInterface;

class AnswerRepository extends BaseRepository implements AnswerRepositoryInterface
{
    public function __construct(Answer $model)
    {
        parent::__construct($model);
    }
} 