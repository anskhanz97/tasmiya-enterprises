<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the services table to store service offerings.
     *
     * Services belong to divisions and can be provided by multiple profiles.
     * This table tracks what services Tasmiya Enterprises offers.
     *
     * Key Columns:
     * - name: Service name (e.g., "Tax Consultation")
     * - slug: URL-friendly identifier auto-generated from name
     * - description: Short description for listings
     * - long_description: Full description for detail pages
     * - icon_url, image_url: Visual assets for display
     * - base_price: Starting price (consultation may vary)
     * - currency: Support for multiple currencies (PKR, USD, EUR, GBP)
     * - division_id: Which division offers this service
     * - is_active: Control visibility without deleting
     *
     * Indexes:
     * - division_id: Common filtering (show services for a division)
     * - is_active: Filter visible services
     * - slug: Direct lookup by slug for routing
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description'); // Short description (max 500 chars)
            $table->longText('long_description')->nullable(); // Full description

            // Media & Presentation
            $table->string('icon_url')->nullable(); // Icon for card display
            $table->string('image_url')->nullable(); // Hero image for detail page

            // Pricing Information
            $table->decimal('base_price', 10, 2); // Starting price in base currency
            $table->string('currency', 3)->default('PKR'); // Currency code

            // Relations & Status
            $table->foreignId('division_id')
                ->constrained('divisions')
                ->cascadeOnDelete(); // If division deleted, services deleted too

            $table->boolean('is_active')->default(true); // Soft visibility control

            // Timestamps
            $table->timestamps();

            // Indexes for Common Queries
            $table->index('division_id'); // Filter by division
            $table->index('is_active'); // Filter active services
            $table->index('slug'); // Quick lookup by slug
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
