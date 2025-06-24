<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
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
        'status',
        'created_by',
        'template_id',
        'template_version_id',
        'settings',
        'expires_at',
        'max_responses',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'settings' => 'array',
        'expires_at' => 'datetime',
        'max_responses' => 'integer',
    ];

    /**
     * Get the user who created this survey.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the template this survey is based on.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the template version this survey is based on.
     */
    public function templateVersion(): BelongsTo
    {
        return $this->belongsTo(TemplateVersion::class);
    }

    /**
     * Get the pages for this survey.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(SurveyPage::class);
    }

    /**
     * Get the responses for this survey.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Check if survey is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if survey is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if survey has reached max responses
     */
    public function hasReachedMaxResponses(): bool
    {
        if (!$this->max_responses) {
            return false;
        }
        
        return $this->responses()->count() >= $this->max_responses;
    }

    /**
     * Check if survey can accept responses
     */
    public function canAcceptResponses(): bool
    {
        return $this->isActive() && !$this->isExpired() && !$this->hasReachedMaxResponses();
    }
}
