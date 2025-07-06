<?php

namespace App\Policies;

use App\Models\Template;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TemplatePolicy
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
    public function view(?User $user, Template $template): bool
    {
        if ($template->is_public) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        return $user->id === $template->created_by;
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
    public function update(User $user, Template $template): bool
    {
        return $user->id === $template->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Template $template): bool
    {
        return $user->id === $template->created_by;
    }

    /**
     * Determine whether the user can fork the model.
     */
    public function fork(User $user, Template $template): bool
    {
        // A user can fork a template if it's public or they own it.
        return $template->is_public || $user->id === $template->created_by;
    }
} 