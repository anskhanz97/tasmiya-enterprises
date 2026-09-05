<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, url, json, boolean
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default social media settings
        DB::table('site_settings')->insert([
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/tasmiyaenterprises',
                'type' => 'url',
                'description' => 'Facebook page URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/tasmiyaent',
                'type' => 'url',
                'description' => 'Twitter/X profile URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'https://linkedin.com/company/tasmiya',
                'type' => 'url',
                'description' => 'LinkedIn company page URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/tasmiyaenterprises',
                'type' => 'url',
                'description' => 'Instagram profile URL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
