<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\Profile;
use App\Models\Service;
use App\Models\ServiceOffering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileServiceOfferingTest extends TestCase
{
    use RefreshDatabase;

    private function makeProfile(Division $division): Profile
    {
        $user = User::factory()->create([
            'role' => 'team_member',
            'division_id' => $division->id,
            'is_active' => true,
        ]);

        $profile = new Profile();
        $profile->user_id = $user->id;
        $profile->is_visible = true;
        $profile->save();

        return $profile;
    }

    private function makeDivision(): Division
    {
        return Division::create([
            'name' => 'FBR Taxation',
            'slug' => 'fbr-taxation',
            'theme_color' => '#1e3a8a',
            'primary_color' => '#1e3a8a',
            'secondary_color' => '#748bc6',
        ]);
    }

    private function makeService(Division $division): Service
    {
        return Service::create([
            'name' => 'Tax Consultation',
            'description' => 'Practical tax advice.',
            'base_price' => 5000,
            'currency' => 'PKR',
            'division_id' => $division->id,
            'is_active' => true,
        ]);
    }

    public function test_owner_can_edit_own_price_without_changing_another_specialist(): void
    {
        $division = $this->makeDivision();
        $first = $this->makeProfile($division);
        $second = $this->makeProfile($division);
        $service = $this->makeService($division);
        $firstOffering = ServiceOffering::create(['profile_id' => $first->id, 'service_id' => $service->id, 'price' => 5000, 'currency' => 'PKR']);
        $secondOffering = ServiceOffering::create(['profile_id' => $second->id, 'service_id' => $service->id, 'price' => 9000, 'currency' => 'PKR']);

        $this->actingAs($first->user)->get(route('profiles.services.index', $first))->assertOk()->assertSee('Choose an icon');
        $this->actingAs($first->user)->get(route('profiles.services.index', $second))->assertForbidden();

        $this->actingAs($first->user)->put(route('profiles.services.update', [$second, $secondOffering]), [
            'card_title' => 'Changed', 'card_description' => 'Changed', 'price' => 1,
            'currency' => 'PKR', 'icon_key' => 'tax',
        ])->assertForbidden();

        $this->actingAs($first->user)->put(route('profiles.services.update', [$first, $secondOffering]), [
            'card_title' => 'Changed', 'card_description' => 'Changed', 'price' => 1,
            'currency' => 'PKR', 'icon_key' => 'tax',
        ])->assertNotFound();

        $this->actingAs($first->user)->put(route('profiles.services.update', [$first, $firstOffering]), [
            'card_title' => 'Personal tax review', 'card_description' => 'A tailored review of your tax position.',
            'price' => 6500, 'currency' => 'PKR', 'icon_key' => 'audit', 'tags' => 'Filing, Planning',
        ])->assertRedirect(route('profiles.services.index', $first));

        $this->assertSame('6500.00', $firstOffering->fresh()->price);
        $this->assertSame(['Filing', 'Planning'], $firstOffering->fresh()->tags);
        $this->assertSame('9000.00', $secondOffering->fresh()->price);
        $this->get('/services')->assertOk()->assertSee('Personal tax review')->assertSee('6,500')->assertSee('9,000');
        $this->get(route('services.show', $service) . '?profile=' . $first->id)
            ->assertOk()->assertSee('Personal tax review')->assertSee('6,500');
    }

    public function test_owner_can_add_and_remove_a_custom_card(): void
    {
        $division = $this->makeDivision();
        $profile = $this->makeProfile($division);

        $this->actingAs($profile->user)->post(route('profiles.services.store', $profile), [
            'source' => 'new', 'card_title' => 'Business tax setup',
            'card_description' => 'Registration and first-return preparation.',
            'price' => 7200, 'currency' => 'PKR', 'icon_key' => 'tax', 'tags' => 'Registration, Filing',
        ])->assertRedirect(route('profiles.services.index', $profile));

        $offering = ServiceOffering::firstOrFail();
        $this->assertSame($profile->id, $offering->service->created_by_profile_id);
        $this->get('/services')->assertOk()->assertSee('Business tax setup');

        $this->actingAs($profile->user)->delete(route('profiles.services.destroy', [$profile, $offering]))
            ->assertRedirect(route('profiles.services.index', $profile));

        $this->assertDatabaseMissing('service_profile', ['id' => $offering->id]);
        $this->assertFalse($offering->service->fresh()->is_active);
        $this->get('/services')->assertOk()->assertDontSee('Business tax setup');
    }

    public function test_existing_service_can_be_added_only_once_to_its_division(): void
    {
        $division = $this->makeDivision();
        $profile = $this->makeProfile($division);
        $service = $this->makeService($division);
        $payload = [
            'source' => 'existing', 'service_id' => $service->id,
            'price' => 8800, 'currency' => 'PKR', 'icon_key' => 'audit', 'tags' => 'Review',
        ];

        $this->actingAs($profile->user)->post(route('profiles.services.store', $profile), $payload)
            ->assertRedirect(route('profiles.services.index', $profile));
        $this->assertDatabaseHas('service_profile', ['service_id' => $service->id, 'profile_id' => $profile->id, 'price' => 8800]);

        $this->actingAs($profile->user)->post(route('profiles.services.store', $profile), $payload)->assertStatus(409);
        $this->assertSame(1, ServiceOffering::count());
    }

    public function test_admin_can_manage_a_team_members_card(): void
    {
        $division = $this->makeDivision();
        $profile = $this->makeProfile($division);
        $service = $this->makeService($division);
        $offering = ServiceOffering::create(['profile_id' => $profile->id, 'service_id' => $service->id]);
        $admin = User::factory()->create(['role' => 'admin', 'division_id' => $division->id]);

        $this->actingAs($admin)->get(route('profiles.services.index', $profile))->assertOk();
        $this->actingAs($admin)->put(route('profiles.services.update', [$profile, $offering]), [
            'card_title' => 'Tax advisory', 'card_description' => 'Tailored business tax advice.',
            'price' => 7800, 'currency' => 'PKR', 'icon_key' => 'tax', 'tags' => 'Planning',
        ])->assertRedirect(route('profiles.services.index', $profile));

        $this->assertSame('7800.00', $offering->fresh()->price);
    }

    public function test_team_flip_cards_render_visible_contact_icons_and_working_links(): void
    {
        $profile = $this->makeProfile($this->makeDivision());
        $profile->update(['social_links' => [
            'phone' => '+92 312 4246916',
            'email' => 'specialist@example.com',
            'linkedin' => 'linkedin.com/in/specialist',
        ]]);

        $this->get('/team')->assertOk()
            ->assertSee('class="team-contact-link"', false)
            ->assertSee('href="tel:+923124246916"', false)
            ->assertSee('href="mailto:specialist@example.com"', false)
            ->assertSee('href="https://linkedin.com/in/specialist"', false)
            ->assertDontSee('fa-envelope');
    }

    public function test_member_menu_links_to_dashboard_and_own_service_cards(): void
    {
        $profile = $this->makeProfile($this->makeDivision());

        $this->actingAs($profile->user)->get('/')
            ->assertOk()
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertSee('href="'.route('profiles.services.index', $profile).'"', false)
            ->assertSee('Add / Manage Services')
            ->assertDontSee('href="#profile"', false)
            ->assertDontSee('href="#admin"', false)
            ->assertDontSee('Admin Panel');

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('profiles.services.index', $profile))->assertOk();
    }

    public function test_admin_service_creation_route_is_not_captured_by_service_show_route(): void
    {
        $division = $this->makeDivision();
        $admin = User::factory()->create(['role' => 'admin', 'division_id' => $division->id]);

        $this->actingAs($admin)->get('/services/create')->assertOk();
    }
}
