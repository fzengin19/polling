<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Question extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'page_id',
        'type',
        'title',
        'is_required',
        'help_text',
        'placeholder',
        'config',
        'order_index',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Get the survey page that owns the question.
     */
    public function surveyPage(): BelongsTo
    {
        return $this->belongsTo(SurveyPage::class, 'page_id');
    }

    /**
     * Get the survey through the survey page.
     */
    public function survey()
    {
        return $this->surveyPage->survey();
    }

    /**
     * Get the choices for this question.
     */
    public function choices(): HasMany
    {
        return $this->hasMany(Choice::class);
    }

    /**
     * Get the answers for this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('question-images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
        
        $this->addMediaCollection('question-videos')
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/ogg'])
            ->singleFile();
        
        $this->addMediaCollection('question-documents')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
            ->singleFile();
    }
} 