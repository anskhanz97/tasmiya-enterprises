<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;

/**
 * ProfilePolicy - Authorization rules for profile actions
 * 
 * What is a Policy?
 * ────────────────
 * A Policy class defines WHO can perform WHAT actions on a resource.
 * It centralizes authorization logic for a specific model.
 * 
 * Instead of checking in controller:
 *   if ($user->id === $profile->user_id || $user->isAdmin()) {
 *       // Allow edit
 *   }
 * 
 * We define it once in policy:
 *   public function update(User $user, Profile $profile): bool
 *       return $user->id === $profile->user_id || $user->isAdmin();
 *   }
 * 
 * Then use in controller:
 *   $this->authorize('update', $profile);
 *   // Throws 403 if policy returns false
 * 
 * Policy Registration:
 * Must be registered in AppServiceProvider for Laravel to find it
 * Gate::policy(Profile::class, ProfilePolicy::class);
 * 
 * Standard Policy Methods:
 * - viewAny(): Can list/view collection?
 * - view(): Can view single resource?
 * - create(): Can create new resource?
 * - update(): Can update existing resource?
 * - delete(): Can delete resource?
 * - restore(): Can restore soft-deleted resource?
 * - forceDelete(): Can permanently delete?
 * 
 * This Policy:
 * Defines authorization for profile viewing, editing, and deletion
 * - View: Anyone can view public profiles
 * - Edit: Only owner or admin
 * - Delete: Only admin
 * - Restore/ForceDelete: Not implemented (no soft deletes)
 */
class ProfilePolicy
{
    /**
     * View any profiles (check if user can see profile list/showcase)
     * 
     * Usage in controller:
     * $this->authorize('viewAny', Profile::class);
     * 
     * For team showcase page: Everyone can view the list
     * Restricted profiles are handled in query scope
     * 
     * Returns: true (public team showcase)
     * 
     * @param User|null $user - Authenticated user or null for guest
     * @return bool - true if can view profile list
     */
    public function viewAny(?User $user): bool
    {
        // Everyone can view the team showcase page
        return true;
    }

    /**
     * View a specific profile
     * 
     * Authorization rules for viewing a single profile:
     * 1. If profile is visible → anyone can view (public)
     * 2. If profile is hidden → only owner or admin can view
     * 
     * Usage in controller:
     * $this->authorize('view', $profile);
     * 
     * Example scenarios:
     * ─────────────────
     * Scenario 1: Public profile
     *   - $profile->is_visible = true
     *   - Policy returns true
     *   - Visitor (guest) can see it
     * 
     * Scenario 2: Hidden profile
     *   - $profile->is_visible = false
     *   - Visitor (guest) tries to access
     *   - $user = null
     *   - Policy returns false → 403 Forbidden
     * 
     * Scenario 3: Hidden profile, owner tries to access
     *   - $profile->is_visible = false
     *   - $user->id === $profile->user_id
     *   - Policy returns true → allowed
     * 
     * Scenario 4: Hidden profile, admin tries to access
     *   - $profile->is_visible = false
     *   - $user->isAdmin() = true
     *   - Policy returns true → allowed
     * 
     * @param User|null $user - Authenticated user or null for guest
     * @param Profile $profile - Profile being viewed
     * @return bool - true if can view
     */
    public function view(?User $user, Profile $profile): bool
    {
        // Public profiles anyone can view
        if ($profile->is_visible) {
            return true;
        }

        // Hidden profiles: only owner or admin can view
        if ($user === null) {
            return false; // Guest cannot view hidden
        }

        // Check if owner
        if ($user->id === $profile->user_id) {
            return true;
        }

        // Check if admin
        if ($user->isAdmin()) {
            return true;
        }

        // Otherwise denied
        return false;
    }

    /**
     * Update a profile
     * 
     * Authorization rules for editing:
     * 1. User can edit their own profile
     * 2. Admin can edit any profile
     * 3. No one else can edit
     * 
     * Usage in controller:
     * $this->authorize('update', $profile);
     * 
     * In form request (ProfileUpdateRequest):
     * Also called to prevent form processing
     * 
     * Decision Tree:
     * ──────────────
     * Can user edit profile?
     *     ├─ Is user the owner? → YES: Allow
     *     ├─ Is user an admin? → YES: Allow
     *     └─ Otherwise → DENY (403 Forbidden)
     * 
     * Example scenarios:
     * ─────────────────
     * Scenario 1: User edits own profile
     *   - $user->id = 1
     *   - $profile->user_id = 1
     *   - $user->id === $profile->user_id → true
     *   - Returns: true ✓
     * 
     * Scenario 2: Admin edits team member's profile
     *   - $user->role = 'admin'
     *   - $profile->user_id = 2 (different user)
     *   - $user->isAdmin() → true
     *   - Returns: true ✓
     * 
     * Scenario 3: User tries to edit someone else's profile
     *   - $user->id = 1
     *   - $profile->user_id = 2
     *   - $user->isAdmin() → false
     *   - Returns: false ✗ (403 Forbidden)
     * 
     * Scenario 4: Guest tries to edit profile
     *   - $user = null (not authenticated)
     *   - Method assumes authenticated user
     *   - Middleware prevents reaching this point
     *   - But for safety: Returns false ✗
     * 
     * @param User $user - Authenticated user
     * @param Profile $profile - Profile being edited
     * @return bool - true if can edit
     */
    public function update(User $user, Profile $profile): bool
    {
        // User can edit their own profile
        if ($user->id === $profile->user_id) {
            return true;
        }

        // Admin can edit any profile
        if ($user->isAdmin()) {
            return true;
        }

        // Everyone else is denied
        return false;
    }

    /**
     * Delete a profile
     * 
     * Authorization rules for deletion:
     * Only admin can delete profiles
     * 
     * Note about deletion:
     * - Deleting profile ≠ deleting user account
     * - Profile deletion just removes from system
     * - User account still exists
     * - Alternative: set is_visible = false instead
     * 
     * Usage in controller:
     * $this->authorize('delete', $profile);
     * 
     * Soft delete alternative (not implemented here):
     * // In migration:
     * $table->softDeletes(); // Adds deleted_at column
     * 
     * // In model:
     * use SoftDeletes;
     * 
     * // Then use:
     * $profile->delete(); // Soft delete (is_visible in our case)
     * $profile->restore(); // Un-delete
     * $profile->forceDelete(); // Permanent delete
     * 
     * @param User $user - Authenticated user
     * @param Profile $profile - Profile being deleted
     * @return bool - true if can delete
     */
    public function delete(User $user, Profile $profile): bool
    {
        // Only admins can delete profiles
        // (Prevents accidental or malicious deletion)
        return $user->isAdmin();
    }

    /**
     * Restore a deleted profile
     * 
     * Currently not implemented (no soft deletes)
     * 
     * If we add soft deletes in future:
     * @param User $user
     * @param Profile $profile
     * @return bool
     */
    public function restore(User $user, Profile $profile): bool
    {
        // Only super_admin can restore
        return $user->role === 'super_admin';
    }

    /**
     * Permanently delete a profile
     * 
     * Currently not implemented (no soft deletes)
     * 
     * If we add soft deletes in future:
     * @param User $user
     * @param Profile $profile
     * @return bool
     */
    public function forceDelete(User $user, Profile $profile): bool
    {
        // Only super_admin can permanently delete
        return $user->role === 'super_admin';
    }
}
