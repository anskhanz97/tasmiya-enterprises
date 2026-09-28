<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->json('presentation_sections')->nullable();
            $table->boolean('hide_profile_image')->default(false);
            $table->boolean('hide_banner_image')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['presentation_sections', 'hide_profile_image', 'hide_banner_image']);
        });
    }
};
