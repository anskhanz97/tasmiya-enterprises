<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Testimonial;

/**
 * TestimonialPolicy
 *
 * Defines authorization rules for Testimonial model.
 *
 * Authorization Flow:
 * - Public can: View approved testimonials
 * - Admin can: View all, approve, reject, feature, delete testimonials
 * - Users can: Submit testimonials (public form)
 *
 * No "update" method - testimonials cannot be edited after submission.
 * Users must delete and resubmit if they made a mistake.
 *
 * Usage Examples:
 * ```php
 * // Check if user can approve testimonials
 * $this->authorize('approve', $testimonial);
 *
 * // In blade view
 * @can('approve', $testimonial)
 *     <button>Approve Review</button>
 * @endcan
 * ```
 */
class TestimonialPolicy
{
    /**
     * Determine whether the user can view any testimonials.
     *
     * @param User|null $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        // Public can view testimonials (controlled by is_approved flag in query)
        return true;
    }

    /**
     * Determine whether the user can view a specific testimonial.
     *
     * Only approved testimonials are visible to public.
     * Admins can see all testimonials (including pending).
     *
     * @param User|null $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function view(?User $user, Testimonial $testimonial): bool
    {
        // Approved testimonials are public
        if ($testimonial->is_approved) {
            return true;
        }

        // Admin can view all testimonials
        if ($user && $user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can approve a testimonial.
     *
     * Only admins can approve testimonials for public display.
     * This prevents inappropriate reviews from appearing on the website.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function approve(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reject a testimonial.
     *
     * Only admins can reject (hide) testimonials.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function reject(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can mark testimonial as featured.
     *
     * Featured testimonials appear first on service pages.
     * Only admins can feature testimonials.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function feature(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete a testimonial.
     *
     * Only admins can delete inappropriate or spam testimonials.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore a deleted testimonial.
     *
     * Admins can restore deleted testimonials.
     * (Would apply if soft deletes are implemented in future)
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function restore(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete a testimonial.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function forceDelete(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }
}
