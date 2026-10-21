<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_returns_to_the_homepage_and_ends_workspace_access(): void
    {
        $this->actingAs(User::factory()->create())->withSession(['private_workspace_value' => 'clear on logout']);

        $this->post(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionMissing('private_workspace_value');

        $this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
