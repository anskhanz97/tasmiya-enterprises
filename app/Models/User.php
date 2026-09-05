<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model - Represents team members and admin users
 * 
 * This model handles:
 * - Authentication (login/logout)
 * - Authorization (roles and permissions)
 * - User profile information
 * - Division assignment
 * - Activity tracking (last_login_at)
 * 
 * User Roles:
 * - 'guest': Default role for non-authenticated users (not stored in DB)
 * - 'team_member': Can view their profile, edit only their own profile
 * - 'admin': Can manage team members, edit any profile, access dashboard
 * - 'super_admin': Full system access (future use)
 * 
 * Why this structure:
 * - Centralizes authentication logic
 * - Role-based access control enables fine-grained permissions
 * - Tracks user activity for analytics and security
 * - Relationship to Division allows scaling to multiple divisions
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Fillable columns - Can be mass-assigned
     * 
     * Example: User::create(['name' => 'Atif Safdar', 'email' => 'atif@tasmiya.com', ...])
     * 
     * Security note: We DON'T include 'password' because it requires hashing.
     * Instead, use: $user->password = Hash::make($password);
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'division_id',
        'is_active',
        'phone',
        'whatsapp_number',
        'bio',
        'profile_image_url',
        'last_login_at',
    ];

    /**
     * Hidden columns - Never included in JSON responses
     * 
     * Security: Don't leak passwords or tokens in API responses
     * Example: json_encode($user) won't include 'password'
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting for columns
     * 
     * 'password' => 'hashed': 
     * - When you set $user->password = 'plaintext', it auto-hashes
     * - Uses bcrypt algorithm (Laravel default, very secure)
     * 
     * '*_at' => 'datetime':
     * - Converts to Carbon date objects
     * - Allows methods like ->diffForHumans(), ->format()
     * 
     * 'boolean' fields:
     * - Converts 0/1 to false/true
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ========== RELATIONSHIPS ==========

    /**
     * Get the division this user belongs to
     * 
     * Example:
     *   $user = User::find(1);
     *   $division = $user->division; // Returns Division model instance
     *   echo $division->name; // "FBR Taxation"
     * 
     * Why BelongsTo:
     * - One user belongs to ONE division
     * - One division has MANY users
     * - This is the "many" side of the relationship
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the profile for this user
     * 
     * Relationship: One user has one profile
     * 
     * Example:
     *   $user = User::find(1);
     *   $profile = $user->profile; // Returns Profile model instance
     *   echo $profile->bio; // User's biography
     * 
     * Why HasOne:
     * - One user has AT MOST one profile
     * - Profile is optional (created after user creation)
     * - We navigate FROM user TO profile (the owner)
     * 
     * Usage in eager loading:
     *   $users = User::with('profile')->get(); // Load profiles efficiently
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    // ========== AUTHORIZATION METHODS ==========

    /**
     * Check if user is admin or super_admin
     * 
     * Usage: if ($user->isAdmin()) { ... }
     * 
     * Why a method instead of checking $user->role directly:
     * - Single source of truth for "is admin" logic
     * - Easy to change if role structure changes
     * - More readable in code
     * 
     * Returns: boolean
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Check if user is a team member
     * 
     * Usage: if ($user->isTeamMember()) { ... }
     * 
     * Returns: boolean
     */
    public function isTeamMember(): bool
    {
        return $this->role === 'team_member';
    }

    /**
     * Check if user can edit a specific profile
     * 
     * Rules:
     * - User can edit their own profile
     * - Admin can edit any profile
     * - Others cannot edit
     * 
     * Usage: if ($user->canEditProfile($profile)) { ... }
     * 
     * Better approach: Use Policy classes (for Phase 2 implementation)
     * But this method is useful for quick checks in views
     * 
     * Parameters: Profile $profile or User $user (same table)
     * Returns: boolean
     */
    public function canEditProfile(User $profile): bool
    {
        return $this->id === $profile->id || $this->isAdmin();
    }

    /**
     * Check if user is active (not deactivated by admin)
     * 
     * Usage: if ($user->isActive()) { ... }
     * 
     * Inactive users:
     * - Can't log in (checked in AuthController)
     * - Can still be displayed in profiles (read-only)
     * - Are marked for potential removal
     * 
     * Returns: boolean
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Check if user has verified their email
     * 
     * Usage: if ($user->hasVerifiedEmail()) { ... }
     * 
     * This is a Laravel built-in method.
     * email_verified_at is set when user clicks email verification link
     * 
     * Returns: boolean
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    // ========== SCOPES (Reusable Query Methods) ==========

    /**
     * Scope: Get only active users
     * 
     * Usage: User::active()->get()
     * Returns: QueryBuilder for method chaining
     * 
     * Why scopes exist:
     * - DRY: Don't repeat the same where() clause everywhere
     * - Readable: User::active() is clearer than User::where('is_active', 1)
     * - Chainable: Can combine multiple scopes
     * 
     * Example: User::active()->byDivision(1)->byRole('team_member')->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get users by role
     * 
     * Usage: User::byRole('admin')->get()
     * Returns: All admin users
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope: Get users by division
     * 
     * Usage: User::byDivision(1)->get()
     * Returns: All users in division 1 (FBR Taxation)
     */
    public function scopeByDivision($query, int $divisionId)
    {
        return $query->where('division_id', $divisionId);
    }

    /**
     * Scope: Get only team members (not admin)
     * 
     * Usage: User::teamMembers()->get()
     * Returns: All team member users (4 people initially)
     */
    public function scopeTeamMembers($query)
    {
        return $query->where('role', 'team_member');
    }

    /**
     * Scope: Get recently logged in users
     * 
     * Usage: User::recentlyActive(30)->get()
     * Returns: Users who logged in in the last 30 days
     * 
     * Useful for: Activity tracking, analytics
     */
    public function scopeRecentlyActive($query, int $days = 30)
    {
        return $query->whereDate('last_login_at', '>=', now()->subDays($days));
    }

    // ========== UTILITY METHODS ==========

    /**
     * Get the user's initials (for avatar display)
     * 
     * Example: User with name "Atif Safdar" returns "AS"
     * Usage in Blade: <div class="avatar">{{ $user->initials }}</div>
     */
    public function getInitialsAttribute(): string
    {
        $nameParts = explode(' ', $this->name);
        
        if (count($nameParts) >= 2) {
            return strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get user's full role name (prettified)
     * 
     * Example: 'super_admin' becomes 'Super Admin'
     * Usage: {{ $user->roleName }}
     */
    public function getRoleNameAttribute(): string
    {
        return str(str_replace('_', ' ', $this->role))->title();
    }

    /**
     * Get user profile URL
     * 
     * Example: 'profile/1' for user with id 1
     * Usage: <a href="{{ $user->profileUrl }}">View Profile</a>
     */
    public function getProfileUrlAttribute(): string
    {
        return route('profile.show', $this);
    }
}
