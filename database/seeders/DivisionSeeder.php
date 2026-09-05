<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * DivisionSeeder - Seed the three business divisions
 * 
 * Run with: php artisan db:seed --class=DivisionSeeder
 * Or as part of DatabaseSeeder: php artisan db:seed
 */
class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('divisions')->insert([
            [
                'name' => 'FBR Taxation',
                'slug' => 'fbr-taxation',
                'tagline' => "Let's Be Money Smart",
                'description' => 'Income tax filing, GST compliance, property tax, sales tax optimization',
                'theme_color' => '#1e3a8a',
                'primary_color' => '#3b82f6',
                'secondary_color' => '#d97706',
                'icon_path' => '📊',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT & Digital Services',
                'slug' => 'it-digital',
                'tagline' => 'If you can think it, we can build it',
                'description' => 'Web apps, website design, digital marketing, AI services',
                'theme_color' => '#7c3aed',
                'primary_color' => '#06b6d4',
                'secondary_color' => '#000000',
                'icon_path' => '💻',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technical Support',
                'slug' => 'tech-support',
                'tagline' => 'Let Technology do the Work for you',
                'description' => 'Installation, configuration, maintenance, security systems',
                'theme_color' => '#f97316',
                'primary_color' => '#334155',
                'secondary_color' => '#eab308',
                'icon_path' => '🔧',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
