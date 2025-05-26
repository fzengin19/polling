<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'poll_id',
        'type',
        'value',
        'votes_count'
    ];
    protected $casts = [
        'votes_count' => 'integer',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
