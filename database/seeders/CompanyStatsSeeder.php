<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class CompanyStatsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('company') as $name => $default) {
            $key = 'integration_company_'.$name;
            if (! SiteSetting::where('key', $key)->exists()) {
                SiteSetting::set($key, (string) $default, 'number');
            }
        }
    }
}
