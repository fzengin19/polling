<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyPage extends Model
{
    use HasFactory;

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
}
