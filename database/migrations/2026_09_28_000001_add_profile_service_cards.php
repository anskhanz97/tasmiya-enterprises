<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('created_by_profile_id')->nullable()->constrained('profiles')->nullOnDelete();
        });

        Schema::table('service_profile', function (Blueprint $table) {
            $table->string('card_title', 100)->nullable();
            $table->text('card_description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('icon_key', 30)->nullable();
            $table->json('tags')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('service_profile', function (Blueprint $table) {
            $table->dropColumn(['card_title', 'card_description', 'price', 'currency', 'icon_key', 'tags']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_profile_id');
        });
    }
};
