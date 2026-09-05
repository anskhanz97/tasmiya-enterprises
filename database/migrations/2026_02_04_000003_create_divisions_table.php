<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CreateDivisionsTable Migration
 * 
 * This migration creates the 'divisions' table which stores information about
 * the three main business divisions of Tasmiya Enterprises:
 * 
 * 1. FBR Taxation Services (Led by Atif Safdar)
 * 2. IT & Digital Services (Led by Waseem Asghar & Ans Khan)
 * 3. Technical Support & Installation (Led by Nazim Rauf)
 * 
 * Each division has:
 * - Unique branding (colors, icons, taglines)
 * - Team members assigned to it
 * - Services offered
 * - Portfolio/projects
 * 
 * Why this table:
 * - Centralized storage of division information
 * - Easy to add new divisions in the future
 * - Division-specific colors/themes in one place
 * - Relationships from users, services, projects to divisions
 * 
 * Database Design Principles Applied:
 * - Normalization: Division data is separate from User data
 * - Relationships: Users.division_id references Divisions.id
 * - Indexes: slug is unique (prevents duplicate division slugs)
 * - Scalability: Easy to add new divisions without schema changes
 */
return new class extends Migration
{
    /**
     * Run the migration - Create the divisions table
     * 
     * Called when running: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            // ========== PRIMARY KEY ==========
            
            /**
             * id: Unique identifier for each division
             * Type: BIGINT (large integer)
             * Why BIGINT: Allows for billions of records if needed
             * PRIMARY KEY: Indexed automatically for fast lookups
             * AUTO_INCREMENT: Automatically assigned on insert
             */
            $table->id();

            // ========== REQUIRED FIELDS ==========

            /**
             * name: Division name (e.g., "FBR Taxation", "IT & Digital")
             * Type: VARCHAR(255) - up to 255 characters
             * UNIQUE: No two divisions can have the same name
             * Why: Easy identification, prevents duplicate entries
             */
            $table->string('name')->unique();

            /**
             * slug: URL-friendly version of name
             * Example: "FBR Taxation" becomes "fbr-taxation"
             * Type: VARCHAR(255)
             * UNIQUE: Each division gets a unique slug
             * Usage: Route::get('/divisions/{slug}', ...)->name('divisions.show')
             *        Then use: route('divisions.show', 'fbr-taxation')
             * Why slug: URLs are cleaner with "division/fbr-taxation" vs "division/1"
             */
            $table->string('slug')->unique();

            // ========== OPTIONAL FIELDS ==========

            /**
             * tagline: Catchy phrase describing the division
             * Examples:
             * - "Let's Be Money Smart" (FBR Taxation)
             * - "If you can think it, we can build it" (IT & Digital)
             * - "Let Technology do the Work for you" (Technical Support)
             * Type: VARCHAR(255)
             * nullable: Optional (some divisions might not have taglines)
             */
            $table->string('tagline')->nullable();

            /**
             * description: Detailed description of what the division does
             * Type: TEXT - allows longer content (up to 65K characters)
             * nullable: Optional
             * Example usage: {{ $division->description }}
             */
            $table->text('description')->nullable();

            // ========== BRANDING / STYLING FIELDS ==========

            /**
             * theme_color: Primary color for division's web pages
             * Type: VARCHAR(7) - exactly 7 characters (enough for hex codes)
             * Format: #RRGGBB (e.g., "#1e3a8a" for dark blue)
             * Default: #000000 (black)
             * Why separate colors: Each division has different branding
             * Example: In Blade: style="color: {{ $division->theme_color }}"
             * Alternative: Store in CSS variables for Tailwind theming
             */
            $table->string('theme_color')->default('#000000');

            /**
             * primary_color: Main accent color
             * Similar to theme_color, used for buttons, highlights
             * Default: #000000
             */
            $table->string('primary_color')->default('#000000');

            /**
             * secondary_color: Accent color for secondary elements
             * Used for borders, backgrounds, accents
             * Default: #ffffff (white)
             */
            $table->string('secondary_color')->default('#ffffff');

            /**
             * icon_path: File path to division icon/logo
             * Type: VARCHAR(255) - path as string
             * Format: 'icons/fbr-taxation.svg' or 'storage/icons/tax.png'
             * nullable: Optional (icon might be in database instead)
             * Example usage: <img src="{{ asset($division->icon_path) }}">
             * Or with accessor: <img src="{{ $division->iconUrl }}">
             */
            $table->string('icon_path')->nullable();

            // ========== TIMESTAMPS ==========

            /**
             * created_at: When this division record was created
             * updated_at: When this division record was last updated
             * Type: TIMESTAMP - stores date and time
             * Automatically managed by Laravel
             * Example: $division->created_at->diffForHumans() → "5 days ago"
             * Why: Audit trail, sorting, knowing when data was created/modified
             */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migration - Drop the divisions table
     * 
     * Called when running: php artisan migrate:rollback
     * Useful during development when you need to revert changes
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
