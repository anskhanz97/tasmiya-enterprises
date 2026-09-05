<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects.
     * Public can view all projects.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the project.
     * Public can view individual projects.
     */
    public function view(?User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create projects.
     * Only admins can create projects.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the project.
     * Only admins can update projects.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the project.
     * Only admins can delete projects.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->role === 'admin';
    }
}
