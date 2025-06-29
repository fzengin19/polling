<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Survey extends Model implements HasMedia
{
    use HasFactory, HasRoles, InteractsWithMedia;

    /**
     * The guard name for the model.
     *
     * @var string
     */
    protected $guard_name = 'api';

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
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('survey-banners')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
        
        $this->addMediaCollection('survey-logos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/svg+xml'])
            ->singleFile();
        
        $this->addMediaCollection('survey-attachments')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/csv'])
            ->singleFile();
    }
}
