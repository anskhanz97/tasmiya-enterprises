<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create Profiles Table Migration
 * 
 * PURPOSE:
 * This migration creates the 'profiles' table which stores user profile information.
 * It's separate from the 'users' table to maintain separation of concerns:
 * - Users table: Authentication and authorization (who can access?)
 * - Profiles table: Display and personalization (what do they show?)
 * 
 * DESIGN DECISIONS:
 * 
 * 1. JSON Columns for Flexible Data:
 *    - specializations: Array of skills (["Tax Planning", "Compliance"])
 *    - social_links: Contact information ({"whatsapp": "0300...", "linkedin": "..."})
 *    - qualifications: Education/certifications (["CPA", "BS Accounting"])
 *    - languages: Languages spoken (["Urdu", "English"])
 *    
 *    Why JSON?
 *    - Don't need individual columns for each skill/language/qualification
 *    - Can add/remove items without schema changes
 *    - Laravel automatically casts to arrays via $casts
 *    - Can still be queried if needed (JSON_EXTRACT in MySQL)
 *    
 * 2. Nullable columns:
 *    - Most fields are nullable to allow gradual profile completion
 *    - Users can create a profile and fill it in over time
 *    - Admin can set defaults during seeding
 *    
 * 3. is_visible boolean:
 *    - Controls whether profile appears in team showcase
 *    - Admin feature: can hide/show profiles independently
 *    - Default true: new profiles visible by default
 *    
 * 4. User relationship:
 *    - profile.user_id → users.id (foreign key)
 *    - UNIQUE constraint: One profile per user
 *    - ON DELETE CASCADE: Delete profile if user deleted
 * 
 * PERFORMANCE CONSIDERATIONS:
 * - Index on user_id for fast profile lookups
 * - Index on is_visible for team showcase queries
 * 
 * FUTURE ENHANCEMENTS (Phase 5+):
 * - Image URLs will be replaced with Google Drive integration
 * - Add verification status for qualifications
 * - Add rating/review system
 * - Add portfolio projects table
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the profiles table with all necessary columns and relationships.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key: User Relationship
            // ─────────────────────────────
            // Links profile to user
            // constrained(): Adds foreign key constraint (prevents orphaned profiles)
            // cascadeOnDelete(): Delete profile if user is deleted
            // unique(): Each user has at most one profile
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique();

            // Biography & Introduction
            // ────────────────────────
            // text() = VARCHAR(65535) - allows long text
            // nullable() = Can be empty initially
            // Default: User fills this in when editing profile
            // Example: "Tax expert with 15+ years of experience in FBR compliance"
            $table->text('bio')->nullable();

            // Skills & Specializations
            // ───────────────────────
            // json() = Stores JSON array in database
            // Automatically cast to array by Laravel via $casts = ['specializations' => 'array']
            // Usage in PHP: $profile->specializations = ['Tax Planning', 'Compliance'];
            // Usage in migration: $profile->specializations = json_encode(['Tax Planning', 'Compliance']);
            // Example: ["Tax Planning", "Tax Compliance", "Corporate Tax", "Individual Tax"]
            $table->json('specializations')->nullable();

            // Professional Experience
            // ──────────────────────
            // integer() = BIGINT
            // Represents years in the industry
            // Example: 15 (years)
            // Used for sorting by experience level (upcoming feature)
            $table->integer('experience_years')->nullable();

            // Profile Picture/Avatar
            // ──────────────────────
            // varchar(255) = Standard URL length
            // nullable() = Can use placeholder/default
            // Phase 3: Placeholder URL
            // Phase 5: Integration with Google Drive
            // Will store URLs like: https://drive.google.com/uc?id=1Abc123Xyz789
            $table->string('profile_image_url')->nullable();

            // Cover/Banner Image
            // ──────────────────
            // For professional profile header
            // Different from profile_image_url (which is avatar)
            // Phase 5: Google Drive integration
            // Example: Division-themed background image
            $table->string('banner_image_url')->nullable();

            // Social Links & Contact Info
            // ────────────────────────────
            // json() = Stores object with multiple contact methods
            // Why JSON instead of separate columns?
            // - Flexibility: Can add WhatsApp, LinkedIn, etc. without schema changes
            // - Easy to add new contact methods in future
            // - Used as a unit (all contact info together)
            // Example:
            // {
            //   "whatsapp": "03001234567",
            //   "linkedin": "linkedin.com/in/atifsafdar",
            //   "email": "atif@custom.com",
            //   "website": "www.atifsafdar.com"
            // }
            // 
            // Accessed via: $profile->social_links['whatsapp']
            // or helper method: $profile->getWhatsAppNumber()
            $table->json('social_links')->nullable();

            // Education & Certifications
            // ──────────────────────────
            // json() = Array of qualifications
            // Why JSON?
            // - Avoid creating separate qualifications table
            // - Simple list that doesn't need to be queried individually
            // - Each profile has unique educational background
            // Example: ["CPA", "BS Accounting", "High School Diploma"]
            // Accessed via: $profile->qualifications (auto-cast to array)
            $table->json('qualifications')->nullable();

            // Languages Spoken
            // ────────────────
            // json() = Array of language names
            // For client communication
            // Important for international services
            // Example: ["Urdu", "English", "Arabic", "Pashto"]
            // Helpful for: Client filtering, multilingual support (future feature)
            $table->json('languages')->nullable();

            // Consultation/Service Fee
            // ────────────────────────
            // decimal(8,2) = Can handle up to $999,999.99 with 2 decimals
            // Hourly rate or consultation fee
            // Used for services booking (Phase 5+)
            // Example: 5000.00 (5000 PKR)
            // nullable() = Not all profiles offer paid consultation
            $table->decimal('consultation_fee', 8, 2)->nullable();

            // Visibility Toggle
            // ─────────────────
            // boolean = TINYINT(1) - 0 or 1
            // Controls whether profile shows in team showcase
            // Use case: Temporarily hide a profile, archived members, etc.
            // default(true) = New profiles visible by default
            // Query: Profile::where('is_visible', true)->get() → visible profiles only
            $table->boolean('is_visible')->default(true)->index();

            // Timestamps
            // ──────────
            // created_at: When profile was created
            // updated_at: Last time profile was modified
            // Automatically managed by Laravel
            // Used for: Sorting (newest first), tracking changes, auditing
            $table->timestamps();

            // Indexes for Performance
            // ──────────────────────
            // user_id: Already indexed via foreign key
            // is_visible: Already indexed above (for showcase queries)
            // 
            // Why indexes?
            // - Team showcase query: Profile::where('is_visible', true)->with(['user', 'division'])->get()
            //   Without index: scans every row (slow with thousands of profiles)
            //   With index: O(log n) instead of O(n)
            // - Profile lookup: Profile::where('user_id', $id)->first()
            //   Automatically indexed via foreign key
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Called when running: php artisan migrate:rollback
     * Deletes the profiles table and all data
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
