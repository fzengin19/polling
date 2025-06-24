<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'is_public',
        'created_by',
        'forked_from_template_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the template this template was forked from.
     */
    public function forkedFrom(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'forked_from_template_id');
    }

    /**
     * Get the templates that were forked from this template.
     */
    public function forks(): HasMany
    {
        return $this->hasMany(Template::class, 'forked_from_template_id');
    }

    /**
     * Get the surveys that use this template.
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    /**
     * Get the template versions.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(TemplateVersion::class);
    }
}
