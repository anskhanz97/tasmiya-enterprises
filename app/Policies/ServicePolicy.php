<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;

/**
 * ServicePolicy
 *
 * Defines authorization rules for Service model.
 * Policies centralize authorization logic for cleaner, reusable code.
 *
 * Authorization Flow:
 * 1. Controller calls: $this->authorize('action', Service::class or $service)
 * 2. Laravel looks up ServicePolicy
 * 3. Calls appropriate method (viewAny, view, create, update, delete, etc.)
 * 4. Returns true (allowed) or false (denied)
 * 5. If false, throws 403 Forbidden exception
 *
 * Usage Examples:
 * ```php
 * // Check if user can view all services
 * Gate::authorize('viewAny', Service::class);
 *
 * // Check if user can edit a specific service
 * Gate::authorize('update', $service);
 *
 * // In controller
 * $this->authorize('create', Service::class);
 * ```
 *
 * Note: Service management (create, update, delete) is admin-only.
 * Viewing services is public.
 */
class ServicePolicy
{
    /**
     * Determine whether the user can view any services.
     *
     * Services are public - anyone can see the service listing.
     * This method is typically used for listing/index pages.
     *
     * @param User|null $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        // Public viewing - no auth required
        return true;
    }

    /**
     * Determine whether the user can view a specific service.
     *
     * Only active services can be viewed.
     * Inactive (deleted/archived) services can only be viewed by admins.
     *
     * @param User|null $user
     * @param Service $service
     * @return bool
     */
    public function view(?User $user, Service $service): bool
    {
        // Public can view active services
        if ($service->is_active) {
            return true;
        }

        // Only admin can view inactive services
        if ($user && $user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create services.
     *
     * Only admins can create new services.
     * Service creation is restricted to prevent spam/misuse.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update (edit) a specific service.
     *
     * Only admins can edit services.
     * Individual experts cannot create/edit their own service listings.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function update(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete a specific service.
     *
     * Only admins can delete services.
     * Deletion is irreversible (unlike soft delete).
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore a deleted service.
     *
     * Note: Currently services use hard delete (see migration).
     * If we implement soft deletes in future, this would restore them.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function restore(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete a service.
     *
     * @param User $user
     * @param Service $service
     * @return bool
     */
    public function forceDelete(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }
}
