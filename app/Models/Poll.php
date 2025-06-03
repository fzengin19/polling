<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Poll extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'user_id',
        'max_votes_per_user',
        'starts_at',
        'ends_at',
        'is_public',
        'uuid',
        'anon_id',
    ];

    protected $casts = [
        'max_votes_per_user' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($poll) {
            if (empty($poll->uuid)) {
                $poll->uuid = Str::random(36);
            }
        });
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
