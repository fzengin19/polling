<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

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
} 