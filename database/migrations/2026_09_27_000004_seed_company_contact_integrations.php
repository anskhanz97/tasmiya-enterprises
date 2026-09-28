<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        foreach ([
            'company_email' => 'contact@tasmiya.com',
            'company_whatsapp' => '+92-312-4246916',
            'company_address' => 'Office No.5, First Floor, Mozang Heights, 43 Mozang Rd, Lahore, Pakistan',
        ] as $key => $value) {
            DB::table('site_settings')->insertOrIgnore([
                'key' => 'integration_'.$key,
                'value' => $value,
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Preserve administrator-edited contact values on rollback.
    }
};
