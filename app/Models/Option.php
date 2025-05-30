<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $fillable = [
        'poll_id',
        'type',
        'value',
        'votes_count',
        'order'
    ];
    protected $casts = [
        'votes_count' => 'integer',
        'order' => 'integer'
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
     
}
