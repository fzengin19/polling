<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SurveyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Survey $survey): bool
    {
        // Publicly active surveys can be viewed by anyone.
        if ($survey->status === 'active') {
            return true;
        }
        
        // Guests cannot view non-active surveys.
        if ($user === null) {
            return false;
        }

        // The owner can view their own non-active surveys.
        return $user->id === $survey->created_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Survey $survey): bool
    {
        return $user->id === $survey->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Survey $survey): bool
    {
        return $user->id === $survey->created_by;
    }
} 