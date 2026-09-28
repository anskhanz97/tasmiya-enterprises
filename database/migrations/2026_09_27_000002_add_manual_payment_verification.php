<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_reference', 120)->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
        });
        Schema::table('contact_inquiries', function (Blueprint $table) {
            $table->string('email_delivery_status', 24)->default('not_configured');
            $table->string('whatsapp_delivery_status', 24)->default('not_configured');
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method', 32)->default('bank_transfer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['payment_reference', 'payment_proof_path', 'reviewed_at']);
        });
        Schema::table('contact_inquiries', fn (Blueprint $table) => $table->dropColumn(['email_delivery_status', 'whatsapp_delivery_status']));
        Schema::table('payments', fn (Blueprint $table) => $table->enum('payment_method', ['card','bank_transfer','cash','check'])->default('card')->change());
    }
};
