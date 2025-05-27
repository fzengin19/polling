<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    protected $fillable = [
        'poll_id',
        'option_id',
        'voter_identifier',
        'user_id'
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
