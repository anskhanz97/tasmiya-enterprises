<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the service_profile pivot table for many-to-many relationship.
     *
     * This table tracks which profiles (experts) provide which services.
     *
     * Example Data:
     * service_id | profile_id
     * 1          | 1           (Atif provides "Tax Consultation")
     * 1          | 2           (Waseem also provides "Tax Consultation")
     * 2          | 2           (Waseem provides "Web Development")
     * 3          | 2           (Waseem provides "Cloud Solutions")
     *
     * This allows:
     * - $service->profiles → All experts providing this service
     * - $profile->services → All services this expert offers
     *
     * Unique Constraint:
     * - (service_id, profile_id) must be unique to prevent duplicates
     * - A profile can't be added twice to the same service
     */
    public function up(): void
    {
        Schema::create('service_profile', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete(); // Delete pivot if service deleted

            $table->foreignId('profile_id')
                ->constrained('profiles')
                ->cascadeOnDelete(); // Delete pivot if profile deleted

            // Timestamps
            $table->timestamp('created_at')->useCurrent();

            // Constraints
            $table->unique(['service_id', 'profile_id']); // No duplicates
            $table->index('profile_id'); // Query by profile

            /**
             * Why these indexes and constraints:
             *
             * unique(['service_id', 'profile_id']):
             * - Prevents adding same profile twice to same service
             * - Automatically indexed by Laravel
             *
             * index('profile_id'):
             * - Speeds up queries like "Get all services for profile X"
             * - Foreign key on service_id is already indexed
             *
             * cascadeOnDelete():
             * - If a service is deleted, remove all associations
             * - If a profile is deleted, remove all associations
             * - Keeps data integrity
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_profile');
    }
};
