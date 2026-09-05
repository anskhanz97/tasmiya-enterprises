<?php

namespace App\Policies;

use App\Models\User;

/**
 * UserPolicy - Authorization policies for User model
 * 
 * Purpose: Determine what actions a user can perform on profiles
 * 
 * Think of it as business logic for: "Can this user do this to that profile?"
 * 
 * Examples:
 * - Can Atif edit his own profile? YES
 * - Can Atif edit Waseem's profile? NO (unless Atif is admin)
 * - Can admin edit any profile? YES
 * - Can guest view profiles? YES
 * - Can guest edit profiles? NO
 * 
 * Why policies exist:
 * - Separate authorization logic from controller
 * - Reusable across multiple controllers
 * - Easy to test
 * - Clear, readable code
 * 
 * How to use:
 * In controller:
 *   $this->authorize('view', $profile);      // Check if current user can view
 *   $this->authorize('update', $profile);    // Check if current user can update
 *   $this->authorize('delete', $profile);    // Check if current user can delete
 * 
 * In blade template:
 *   @can('update', $profile)
 *       <a href="/profile/{{ $profile->id }}/edit">Edit</a>
 *   @endcan
 * 
 * If authorization fails:
 *   - authorize() throws AuthorizationException
 *   - Laravel returns 403 Forbidden response
 *   - User sees error page or JSON error
 * 
 * Relationship between Middleware and Policies:
 * 
 * Middleware checks: "Is this user allowed to access this route?"
 * Examples:
 * - middleware('auth') - Must be logged in
 * - middleware('admin') - Must be admin
 * - middleware('verified') - Must have verified email
 * 
 * Policies check: "Can this user perform this action on this resource?"
 * Examples:
 * - Can user edit this profile?
 * - Can user delete this payment?
 * - Can user access this division's data?
 * 
 * Both can be used together:
 * Route::post('/profile/{id}', [ProfileController::class, 'update'])
 *     ->middleware('auth');  // Middleware: Must be logged in
 * 
 * In controller:
 *     public function update(User $profile) {
 *         $this->authorize('update', $profile);  // Policy: Can edit this profile?
 *         // ... update code ...
 *     }
 * 
 * Alternative: Use FormRequest for authorization:
 * class UpdateProfileRequest extends FormRequest {
 *     public function authorize() {
 *         return auth()->user()->can('update', $this->route('profile'));
 *     }
 * }
 * 
 * This is actually the best approach (combines validation + authorization)
 * But showing Policy class for educational value
 */
class UserPolicy
{
    /**
     * Determine if user can view another user's profile
     * 
     * Rules:
     * - Everyone can view profiles (no auth required)
     * - Profiles are public
     * - But we might want to hide some info from non-authenticated users
     * 
     * Current implementation: Anyone can view (return true)
     * 
     * Usage:
     * @can('view', $profile)
     *     Display profile information
     * @endcan
     * 
     * Actually, view is public so we don't need to check in code.
     * This method is here for completeness / future use.
     */
    public function view(User $user, User $profile): bool
    {
        // Everyone (even non-authenticated) can view profiles
        return true;
    }

    /**
     * Determine if user can update a profile
     * 
     * Rules:
     * - User can update their own profile
     * - Admin can update any profile
     * - Otherwise, not allowed
     * 
     * Examples:
     * - Atif (user) trying to edit Atif's profile? YES (same user)
     * - Atif (user) trying to edit Waseem's profile? NO
     * - Admin trying to edit Atif's profile? YES (is admin)
     * - Guest (not logged in) trying to edit? NO (not passed to method)
     * 
     * Usage in controller:
     * public function update(User $profile) {
     *     $this->authorize('update', $profile);
     *     // If not authorized, 403 Forbidden is thrown
     *     // If authorized, continue with update
     * }
     * 
     * Usage in blade:
     * @can('update', $profile)
     *     <a href="/profile/{{ $profile->id }}/edit">Edit Profile</a>
     * @endcan
     * 
     * Key insight:
     * This method is only called if user is authenticated
     * (Because you need to be logged in to have a User object)
     * Non-authenticated users can't reach this logic
     * (Middleware protects the routes)
     * 
     * Parameters:
     * - $user: The currently authenticated user making the request
     * - $profile: The user profile being checked
     * 
     * Both are User model instances
     * 
     * Returns: boolean
     *   true: Allow the action
     *   false: Deny the action (403)
     */
    public function update(User $user, User $profile): bool
    {
        // User can edit their own profile
        if ($user->id === $profile->id) {
            return true;
        }

        // Admin can edit any profile
        if ($user->isAdmin()) {
            return true;
        }

        // All other cases: deny
        return false;
    }

    /**
     * Determine if user can delete a profile
     * 
     * Currently: Only admin can delete profiles
     * 
     * Future consideration:
     * Should we really allow deletion?
     * Better approach: Soft delete (keep data for audit trail)
     * Or just deactivate: is_active = false
     * 
     * Why not allow deletion:
     * - Breaks relationships (payments point to this user)
     * - Loses audit trail (who did what, when)
     * - GDPR allows "right to be forgotten" but we might need data for legal reasons
     * 
     * Implementation notes:
     * If you implement delete, handle cascading:
     * - Delete payments? Reject or handle separately?
     * - Keep testimonials? Anonymize them?
     * - Delete activity logs? No (keep for audit)
     * 
     * For now: This method is not used in routes
     * Just showing pattern for completeness
     */
    public function delete(User $user, User $profile): bool
    {
        // Only admin can delete profiles
        if ($user->isAdmin()) {
            return true;
        }

        // All others: deny
        return false;
    }

    /**
     * Determine if user can restore a deleted profile
     * 
     * Used with soft deletes (not currently implemented)
     * 
     * Soft delete: Record stays in DB, marked as deleted
     * Normal delete: Record removed from DB
     * 
     * Soft delete benefits:
     * - Preserve relationships and references
     * - Easier to restore
     * - Audit trail maintained
     * - GDPR compliance (data kept for legal reasons)
     * 
     * Laravel has built-in soft deletes:
     * use SoftDeletes;
     * 
     * Future Phase: Implement soft deletes for Users
     */
    public function restore(User $user, User $profile): bool
    {
        // Only admin can restore deleted profiles
        return $user->isAdmin();
    }

    /**
     * Determine if user can permanently delete a profile
     * 
     * Used with soft deletes
     * forceDelete() means: Really delete, don't just mark as deleted
     * 
     * This should be very restricted
     * Only super admin and only with reason
     */
    public function forceDelete(User $user, User $profile): bool
    {
        // Only super admin can permanently delete
        return $user->role === 'super_admin';
    }

    /**
     * Intercept all authorization checks
     * 
     * Before Laravel checks specific methods (view, update, etc),
     * it calls before() first.
     * 
     * If before() returns true/false, that's the final answer.
     * If before() returns null, continue to specific method.
     * 
     * Usage: Short-circuit checks (e.g., super admin can do anything)
     * 
     * Current: Return null (use specific methods)
     * 
     * Could implement:
     * public function before(User $user, $ability)
     * {
     *     if ($user->role === 'super_admin') {
     *         return true;  // Super admin can do anything
     *     }
     *     return null;  // Use specific method logic
     * }
     */
}
