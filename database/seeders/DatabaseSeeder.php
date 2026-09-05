<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call all seeders in order
        $this->call([
            DivisionSeeder::class,
            UserSeeder::class,
            ProfileSeeder::class,
            ServiceSeeder::class,
            TestimonialSeeder::class,
        ]);
    }
}
