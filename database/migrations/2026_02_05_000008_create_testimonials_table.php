<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the testimonials table for client reviews and feedback.
     *
     * Testimonials use a POLYMORPHIC relationship pattern, meaning they can
     * belong to either a Service OR a Profile (or both).
     *
     * Polymorphic Example:
     * - testimonials.testimonialable_type = 'App\Models\Service'
     *   testimonials.testimonialable_id = 1
     *   → This testimonial reviews Service #1
     *
     * - testimonials.testimonialable_type = 'App\Models\Profile'
     *   testimonials.testimonialable_id = 2
     *   → This testimonial reviews Profile #2
     *
     * Key Columns:
     * - content: Review text (20-500 characters)
     * - rating: Star rating (1-5)
     * - author_*: Reviewer information (name, position, company, image)
     * - is_approved: Admin approval before displaying (prevents spam)
     * - is_featured: Highlight special/excellent reviews first
     *
     * Workflow:
     * 1. User submits testimonial (is_approved = false)
     * 2. Admin reviews in dashboard
     * 3. Admin approves (is_approved = true)
     * 4. Testimonial displays on website
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();

            // Review Content
            $table->text('content'); // Review text (20-500 chars)
            $table->tinyInteger('rating'); // 1-5 stars

            // Author Information
            $table->string('author_name', 100);
            $table->string('author_position', 100)->nullable(); // Job title
            $table->string('author_company', 100)->nullable(); // Company name
            $table->string('author_image_url')->nullable(); // Avatar image

            // Polymorphic Relationship Columns
            // These columns track WHAT this testimonial belongs to
            $table->string('testimonialable_type'); // Model class name
            // Examples: 'App\Models\Service', 'App\Models\Profile'

            $table->unsignedBigInteger('testimonialable_id'); // ID of service or profile

            // Status Columns
            $table->boolean('is_approved')->default(false); // Admin approval
            $table->boolean('is_featured')->default(false); // Highlight this review

            // Timestamps
            $table->timestamps();

            // Indexes for Common Queries
            // Polymorphic index: Find all testimonials for a specific service/profile
            $table->index(['testimonialable_type', 'testimonialable_id']);

            // Approval index: Find pending testimonials in admin dashboard
            $table->index('is_approved');

            // Featured index: Display featured testimonials first
            $table->index('is_featured');

            /**
             * Why Polymorphic Relationship?
             *
             * Alternative: Separate tables
             * - service_testimonials table
             * - profile_testimonials table
             * - Problem: Code duplication, harder to manage
             *
             * Polymorphic Solution: Single testimonials table
             * - testimonialable_type tells us which model it belongs to
             * - testimonialable_id tells us which instance
             * - Single validation, single approval flow
             * - DRY principle (Don't Repeat Yourself)
             *
             * Example Query:
             * SELECT * FROM testimonials
             * WHERE testimonialable_type = 'App\\Models\\Service'
             * AND testimonialable_id = 1
             * AND is_approved = true
             * ORDER BY is_featured DESC, created_at DESC
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
