<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    use HasFactory;
    protected $fillable = [
        'poll_id',
        'option_id',
        'anon_id',
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
        return $this->belongsTo(User::class);
    }
}
