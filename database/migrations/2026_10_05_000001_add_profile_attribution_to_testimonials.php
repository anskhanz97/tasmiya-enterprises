<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            // Service reviews need an explicit specialist; never infer one from a shared service.
            $table->foreignId('profile_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', fn (Blueprint $table) => $table->dropConstrainedForeignId('profile_id'));
    }
};
