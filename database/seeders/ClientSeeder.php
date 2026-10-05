<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Add seeded brands without replacing any clients an admin has customized.
        $clients = json_decode(SiteSetting::get('home_clients', 'null'), true);
        if (! is_array($clients)) {
            $clients = config('clients');
        } else {
            $existingNames = array_map(fn ($client) => strtolower($client['name'] ?? ''), $clients);
            foreach (config('clients') as $client) {
                if (! in_array(strtolower($client['name']), $existingNames, true)) {
                    $clients[] = $client;
                }
            }
        }

        SiteSetting::set('home_clients', json_encode($clients, JSON_UNESCAPED_SLASHES), 'json');
    }
}
