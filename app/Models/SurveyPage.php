<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SurveyPage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'survey_id',
        'order_index',
        'title',
    ];

    /**
     * Get the survey that owns this page.
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * Get the questions for this page.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'page_id');
    }

    /**
     * Scope to order by order_index
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index', 'asc');
    }

    /**
     * Get next page
     */
    public function getNextPage(): ?self
    {
        return $this->survey->pages()
            ->where('order_index', '>', $this->order_index)
            ->ordered()
            ->first();
    }

    /**
     * Get previous page
     */
    public function getPreviousPage(): ?self
    {
        return $this->survey->pages()
            ->where('order_index', '<', $this->order_index)
            ->ordered()
            ->orderBy('order_index', 'desc')
            ->first();
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('page-images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
        
        $this->addMediaCollection('page-backgrounds')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }
}
