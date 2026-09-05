<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Contact information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();

            // Inquiry details
            $table->string('subject')->nullable();
            $table->longText('message');
            $table->enum('source', ['website', 'email', 'whatsapp', 'phone', 'referral'])->default('website');
            $table->string('source_type')->nullable(); // e.g., 'text', 'document', etc for WhatsApp

            // Status and tracking
            $table->enum('status', ['new', 'contacted', 'in_progress', 'resolved', 'closed'])->default('new');
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->text('notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('phone');
            $table->index('email');
            $table->index('source');
            $table->index('status');
            $table->index('user_id');
            $table->index('assigned_to');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_inquiries');
    }
};
