<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key', 64)->unique();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('address', 200)->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('converted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_contact_id')->constrained()->restrictOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('party_name', 100);
            $table->string('party_email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('phone_normalized', 20)->nullable()->index();
            $table->string('address', 200)->nullable();
            $table->string('source', 30);
            $table->string('lead_status', 30)->default('New');
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['assigned_to', 'lead_status']);
            $table->index(['source', 'phone_normalized', 'lead_status']);
        });
        Schema::create('lead_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->restrictOnDelete();
            $table->string('lead_status', 30);
            $table->string('activity_type', 40);
            $table->date('follow_up_date')->nullable();
            $table->time('follow_up_time')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('assigned_to_name')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('supersedes_id')->nullable()->constrained('lead_details')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['lead_id', 'id']);
            $table->index(['follow_up_date', 'completed_at', 'cancelled_at'], 'lead_open_calendar');
        });
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_message_id', 191)->nullable()->unique();
        });
    }

    public function down(): void
    {
        // CRM history is business data: restore a backup instead of silently dropping it.
        if (\Illuminate\Support\Facades\DB::table('leads')->exists()) {
            throw new RuntimeException('CRM contains history. Back it up and restore the pre-CRM database to roll back.');
        }
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_id');
            $table->dropConstrainedForeignId('division_id');
            $table->dropUnique(['external_message_id']);
            $table->dropColumn('external_message_id');
        });
        Schema::dropIfExists('lead_details');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('lead_contacts');
    }
};
