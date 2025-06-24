<?php

namespace App\Repositories\Abstract;

use App\Models\SurveyPage;
use Illuminate\Database\Eloquent\Collection;

interface SurveyPageRepositoryInterface
{
    /**
     * Find page by ID
     */
    public function find(int $id): ?SurveyPage;

    /**
     * Get all pages for a survey
     */
    public function findBySurvey(int $surveyId): Collection;

    /**
     * Get pages for a survey ordered by order_index
     */
    public function getOrderedPages(int $surveyId): Collection;

    /**
     * Get next order index for a survey
     */
    public function getNextOrderIndex(int $surveyId): int;

    /**
     * Create new page
     */
    public function create(array $data): SurveyPage;

    /**
     * Update page
     */
    public function update(SurveyPage $page, array $data): bool;

    /**
     * Delete page
     */
    public function delete(SurveyPage $page): bool;

    /**
     * Reorder pages
     */
    public function reorder(int $surveyId, array $pageIds): bool;
} 