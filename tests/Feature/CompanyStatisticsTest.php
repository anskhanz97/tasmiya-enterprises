<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\CompanyStatsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_statistics_survive_reseeding_after_an_admin_edit(): void
    {
        $this->seed(CompanyStatsSeeder::class);
        $this->assertSame(['clients_served' => 2500, 'experience_years' => 20], SiteSetting::companyStats());
        $this->get('/')->assertOk()->assertSee('2500+')->assertSee('20+');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('settings.index'))->assertOk()->assertSee('Company statistics');
        $this->actingAs($admin)->put(route('settings.integrations.update'), [
            'section' => 'statistics', 'company_clients_served' => 3200, 'company_experience_years' => 25,
        ])->assertRedirect(route('settings.index').'#ig-statistics');
        $this->seed(CompanyStatsSeeder::class);
        $this->assertSame(['clients_served' => 3200, 'experience_years' => 25], SiteSetting::companyStats());
        $this->get('/')->assertOk()->assertSee('3200+')->assertSee('25+');
        $this->get('/team')->assertOk()->assertSee('25+');
    }

    public function test_statistics_require_admin_access_and_valid_numbers(): void
    {
        $member = User::factory()->create(['role' => 'team_member']);
        $this->actingAs($member)->put(route('settings.integrations.update'), [
            'section' => 'statistics', 'company_clients_served' => 3200, 'company_experience_years' => 25,
        ])->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->put(route('settings.integrations.update'), [
            'section' => 'statistics', 'company_clients_served' => -1, 'company_experience_years' => 'twenty',
        ])->assertSessionHasErrors(['company_clients_served', 'company_experience_years']);
        $this->assertDatabaseMissing('site_settings', ['key' => 'integration_company_clients_served']);
    }
}
