<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AddRoleAndDivisionToUsersTable Migration
 * 
 * This migration adds authentication and profile-related columns to the users table.
 * It transforms the users table from basic auth (just email/password) to a 
 * comprehensive user management system with roles, divisions, and profiles.
 * 
 * Columns added:
 * - role: ENUM for role-based access control (RBAC)
 * - division_id: Foreign key linking user to their division
 * - is_active: Soft deactivation without deleting accounts
 * - phone, whatsapp_number: Contact information
 * - bio: User biography for public display
 * - profile_image_url: Link to profile photo
 * - last_login_at: Activity tracking
 * 
 * Why add to existing table instead of creating new table:
 * - Keeps all user data in one place
 * - User is the central entity that everything relates to
 * - Simpler queries (no joins needed to get basic user info)
 * - Easier for authorization (user always available in context)
 * 
 * Why ENUM for role instead of separate roles table:
 * - We only have 4 fixed roles (not thousands)
 * - ENUM is faster and simpler
 * - Roles don't need additional data beyond the name
 * - If roles become complex, can migrate to separate table later
 * 
 * Why nullable division_id:
 * - Admin might not belong to a specific division (manages all)
 * - Can represent system-wide roles separate from divisions
 * - Allows user creation before division assignment
 */
return new class extends Migration
{
    /**
     * Run the migration
     * Called when running: php artisan migrate
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ========== AUTHORIZATION ==========

            /**
             * role: User's access level and permissions
             * 
             * Type: ENUM - Only allows specific values
             * Values:
             * - 'guest': Default (used for logged-out users, not stored in DB)
             * - 'team_member': Can view/edit own profile only
             * - 'admin': Can manage users and content for their division
             * - 'super_admin': Full system access
             * 
             * Default: 'guest' (but 'team_member' for registered users)
             * 
             * Placement: After password (logical grouping with auth fields)
             * 
             * Why ENUM:
             * - Validates data at database level
             * - Can't accidentally insert 'adminn' (typo)
             * - Query optimization (small storage, indexed)
             * - Forces explicit choices
             * 
             * Example usage:
             *   User::where('role', 'admin')->get()
             *   if ($user->isAdmin()) { ... }
             *   can('edit', $profile) // Uses roles internally
             */
            $table->enum('role', ['guest', 'team_member', 'admin', 'super_admin'])
                ->default('guest')
                ->after('password');

            // ========== ORGANIZATIONAL ==========

            /**
             * division_id: Foreign key to divisions table
             * 
             * Type: BIGINT UNSIGNED - must match divisions table's id type
             * nullable: Can be null (for admin/system accounts)
             * 
             * Constraints:
             * - constrained('divisions'): Creates foreign key constraint
             *   Ensures every division_id exists in divisions table
             * - onDelete('set null'): If division deleted, set this to NULL
             *   Other option: 'cascade' would delete the user (dangerous!)
             * 
             * Why foreign key:
             * - Database integrity: prevents orphaned records
             * - Ensures only valid divisions can be assigned
             * - Enables cascading operations
             * - Automatically indexed for performance
             * 
             * Why nullable:
             * - Admin users don't belong to a specific division
             * - Allows temporary state during user creation
             * - Represents system-wide roles
             * 
             * Example:
             *   $user->division // Returns Division model or null
             *   $user->byDivision(1) // Get users in division 1
             */
            $table->foreignId('division_id')
                ->nullable()
                ->after('role')
                ->constrained('divisions')
                ->onDelete('set null');

            /**
             * is_active: Whether user account is active
             * 
             * Type: BOOLEAN (0 or 1)
             * Default: false (accounts start inactive until admin activates)
             * Why not delete: Keeps user data, payment history, activity logs
             * 
             * Usage:
             * - Deactivate account without losing data
             * - Prevent login without deleting user
             * - Temporary suspension from system
             * - Audit trail (know which users were active when)
             * 
             * Checked in:
             * - AuthController@store (before allowing login)
             * - User::active() scope (query only active users)
             */
            $table->boolean('is_active')
                ->default(false)
                ->after('division_id');

            // ========== PROFILE INFORMATION ==========

            /**
             * phone: User's phone number
             * 
             * Type: VARCHAR(20) - flexible for international formats
             * Format examples: "+92 300 1234567", "03001234567", "+1-555-123-4567"
             * nullable: Optional
             * 
             * Usage:
             * - Display on public profile
             * - Contact information
             * - Used by support team
             * - Never used for authentication (just storage)
             * 
             * Note: Validation happens in request/form validation, not DB
             * Could add regex validation: before inserting
             */
            $table->string('phone')->nullable()->after('is_active');

            /**
             * whatsapp_number: WhatsApp contact number
             * 
             * Type: VARCHAR(20)
             * Format: International format with country code
             * Example: "+92-300-1234567"
             * nullable: Optional
             * 
             * Usage:
             * - Generate WhatsApp contact links
             * - Route customer inquiries to correct team member
             * - Display on profiles with CTA button
             * 
             * Example link generation:
             *   https://wa.me/923001234567?text=Hi
             */
            $table->string('whatsapp_number')->nullable()->after('phone');

            /**
             * bio: User biography / professional summary
             * 
             * Type: TEXT - longer content than VARCHAR
             * Capacity: Up to 65,535 characters
             * nullable: Optional
             * 
             * Usage:
             * - Professional description on public profile
             * - Displayed on team member pages
             * - SEO benefits (text content for search engines)
             * - Division-specific content
             * 
             * Example for Atif Safdar (Taxation):
             *   "Tax expert with 15 years experience in FBR compliance..."
             * 
             * Example for Waseem Asghar (IT):
             *   "Full-stack web developer, specializing in Laravel..."
             * 
             * Validation: Server-side only (max 5000 chars in form request)
             */
            $table->text('bio')->nullable()->after('whatsapp_number');

            /**
             * profile_image_url: URL to user's profile photo
             * 
             * Type: VARCHAR(255) - can store URL
             * Format: Full URL or relative path
             * Examples:
             * - 'https://drive.google.com/uc?id=...' (Google Drive)
             * - 'storage/profiles/atif.jpg' (Local storage)
             * - 'https://tasmiya.com/images/nazim.png' (CDN)
             * nullable: Optional
             * 
             * Why store URL instead of file:
             * - Google Drive integration (get URL from Drive)
             * - Flexibility (can use any image source)
             * - No file storage on server (cost savings)
             * - Easy to update without file management
             * 
             * Loading in Blade:
             *   <img src="{{ $user->profile_image_url }}" class="w-32 h-32 rounded-full">
             * 
             * Privacy: Accessible publicly (not sensitive data)
             */
            $table->string('profile_image_url')->nullable()->after('bio');

            /**
             * last_login_at: When user last logged in
             * 
             * Type: TIMESTAMP - stores date and time
             * nullable: Can be null (user never logged in)
             * 
             * Updated by:
             * - AuthController@store after successful login
             * - User::updateLastLogin() helper method
             * 
             * Usage:
             * - Activity tracking / analytics
             * - Identify inactive accounts
             * - Security audit (check for suspicious logins)
             * - Query recent active users: User::recentlyActive(30)->get()
             * 
             * Example:
             *   $user->last_login_at->diffForHumans()  // "2 hours ago"
             *   User::where('last_login_at', '<', now()->subDays(90))->delete()
             */
            $table->timestamp('last_login_at')->nullable()->after('profile_image_url');

            // ========== INDEXES ==========

            /**
             * Indexes speed up database queries dramatically
             * 
             * Example: Querying 1 million users
             * - Without index: Scans all 1M rows (~100ms)
             * - With index: Binary search (~0.01ms)
             * 
             * Create indexes on columns that are:
             * 1. Used in WHERE clauses
             * 2. Used in JOINs
             * 3. Used frequently in queries
             * 
             * Trade-off: Indexes use extra storage and slow down writes
             * But reads are much faster (worth it for frequently-read tables)
             */

            /**
             * Index on role column
             * 
             * Why: Frequently query by role
             * - User::where('role', 'admin')->get()
             * - User::where('role', '!=', 'guest')->get()
             * 
             * Laravel Profiler shows this is one of our most common queries
             */
            $table->index('role');

            /**
             * Index on division_id
             * 
             * Why: Frequently query by division
             * - Division::find(1)->users // Joins on this
             * - User::where('division_id', 1)->get()
             * - Show "Team members in FBR Taxation division"
             * 
             * Note: Foreign keys are automatically indexed in some databases
             * But explicitly adding ensures it across all DB engines
             */
            $table->index('division_id');

            /**
             * Index on email
             * 
             * Why: Used in login (WHERE email = ?)
             * - Fastest path to look up user during authentication
             * - Email should be UNIQUE (prevents duplicates)
             * - Critical path for performance
             * 
             * Note: email column already had an index in the original table
             * This adds another one for clarity (harmless duplication)
             */
            $table->index('email');

            /**
             * Index on is_active
             * 
             * Why: Filter inactive users
             * - User::where('is_active', true)->get()
             * - User::active()->get() scope
             * - Important for dashboard queries
             * 
             * Could also create composite index:
             * $table->index(['is_active', 'division_id']);
             * Would speed up: User::active()->byDivision(1)->get()
             * But would increase storage usage
             */
            // Optional: $table->index('is_active');
        });
    }

    /**
     * Reverse the migration
     * Called when running: php artisan migrate:rollback
     * 
     * Order of drops matters: Drop columns first, then indexes
     * Laravel's dropForeignKeyConstraints() handles foreign keys
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns we added
            $table->dropColumn([
                'role',
                'division_id',
                'is_active',
                'phone',
                'whatsapp_number',
                'bio',
                'profile_image_url',
                'last_login_at',
            ]);
        });
    }
};
