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
        'starts_at',
        'ends_at',
        'is_public',
        'uuid',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(fn($poll) => $poll->uuid = (string) Str::uuid());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}