<?php

namespace Tests\Feature;

use App\Models\{Division, Profile, Service, ServiceOffering, Testimonial, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileReviewTest extends TestCase
{
    use RefreshDatabase;

    private function profile(): Profile
    {
        $division = Division::firstOrCreate(['slug' => 'digital'], ['name' => 'Digital', 'theme_color' => '#6550a7', 'primary_color' => '#6550a7', 'secondary_color' => '#9380bd']);
        $user = User::factory()->create(['role' => 'team_member', 'division_id' => $division->id, 'is_active' => true]);
        $profile = new Profile;
        $profile->user_id = $user->id;
        $profile->is_visible = true;
        $profile->save();
        return $profile;
    }

    private function data(array $extra = []): array
    {
        return array_replace(['author_name' => 'Actual client', 'rating' => 5, 'content' => 'Excellent support for our business project.', 'is_approved' => 1], $extra);
    }

    public function test_owner_can_manage_reviews_and_public_profile_only_shows_published_personal_feedback(): void
    {
        $owner = $this->profile();
        $other = $this->profile();
        $this->actingAs($owner->user)->get(route('profiles.reviews.index', $owner))->assertOk()->assertSee('Add a client review');
        $this->post(route('profiles.reviews.store', $owner), $this->data())->assertRedirect();
        $review = Testimonial::firstOrFail();
        $manager = $this->get(route('profiles.reviews.index', $owner))->assertOk();
        $manager->assertSee('View published review')->assertSee('#review-'.$review->id)->assertSee('form="review-edit-'.$review->id.'"', false)->assertSee('form="review-delete-'.$review->id.'"', false);
        $this->assertSame(1, substr_count($manager->getContent(), $review->content));
        $this->get(route('profiles.show', $owner))->assertOk()->assertSee($review->content)->assertSee('Manage my reviews')->assertDontSee('Michael Chen');
        $this->get(route('profiles.show', $other))->assertOk()->assertDontSee($review->content);
        $this->put(route('profiles.reviews.update', [$owner, $review]), $this->data(['is_approved' => 0, 'content' => 'Updated genuine client feedback for this person.']))->assertRedirect();
        $this->get(route('profiles.show', $owner))->assertDontSee('Updated genuine client feedback');
        $this->delete(route('profiles.reviews.destroy', [$owner, $review]))->assertRedirect();
        $this->assertDatabaseMissing('testimonials', ['id' => $review->id]);
    }

    public function test_reviews_cannot_be_managed_through_another_profile_or_forged_target(): void
    {
        $owner = $this->profile();
        $other = $this->profile();
        $review = $other->testimonials()->create($this->data());
        $this->get(route('profiles.reviews.index', $owner))->assertRedirect(route('login'));
        $this->actingAs($owner->user)->get(route('profiles.reviews.index', $other))->assertForbidden();
        $this->post(route('profiles.reviews.store', $other), $this->data())->assertForbidden();
        $this->put(route('profiles.reviews.update', [$owner, $review]), $this->data())->assertNotFound();
        $this->delete(route('profiles.reviews.destroy', [$owner, $review]))->assertNotFound();
        $this->post(route('profiles.reviews.store', $owner), $this->data(['profile_id' => $other->id, 'testimonialable_id' => $other->id]))->assertRedirect();
        $this->assertSame($owner->id, Testimonial::latest('id')->first()->profile_id);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get(route('profiles.reviews.index', $other))->assertOk();
    }

    public function test_shared_service_reviews_and_ratings_are_scoped_to_selected_specialist(): void
    {
        $first = $this->profile();
        $second = $this->profile();
        $service = Service::create(['name' => 'Web Development', 'description' => 'Business websites.', 'base_price' => 50000, 'currency' => 'PKR', 'division_id' => $first->user->division_id, 'is_active' => true]);
        foreach ([$first, $second] as $profile) ServiceOffering::create(['profile_id' => $profile->id, 'service_id' => $service->id]);
        $this->actingAs($first->user)->post(route('profiles.reviews.store', $first), $this->data(['service_id' => $service->id]))->assertRedirect();
        $this->actingAs($second->user)->post(route('profiles.reviews.store', $second), $this->data(['service_id' => $service->id, 'rating' => 1, 'content' => 'Feedback specifically about the second specialist.']))->assertRedirect();
        $this->get(route('profiles.show', $first))->assertSee('Excellent support')->assertDontSee('Feedback specifically');
        $this->get(route('services.show', $service).'?profile='.$first->id)->assertOk()->assertSee('Excellent support')->assertDontSee('Feedback specifically')->assertViewHas('averageRating', fn ($rating) => (float)$rating === 5.0);
        $this->get(route('services.show', $service))->assertSee('Excellent support')->assertSee('Feedback specifically');
        $outsider = $this->profile();
        $this->actingAs($outsider->user)->post(route('profiles.reviews.store', $outsider), $this->data(['service_id' => $service->id]))->assertSessionHasErrors('service_id');
    }

    public function test_public_submission_preserves_specialist_attribution_and_cannot_publish_itself(): void
    {
        $profile = $this->profile();
        $other = $this->profile();
        $service = Service::create(['name' => 'Consultation', 'description' => 'Advice.', 'base_price' => 5000, 'currency' => 'PKR', 'division_id' => $profile->user->division_id, 'is_active' => true]);
        ServiceOffering::create(['profile_id' => $profile->id, 'service_id' => $service->id]);
        $this->post(route('testimonials.store'), $this->data(['profile_id' => $profile->id, 'service_id' => $service->id]))->assertRedirect();
        $this->assertDatabaseHas('testimonials', ['profile_id' => $profile->id, 'testimonialable_id' => $service->id, 'testimonialable_type' => Service::class, 'is_approved' => false]);
        $this->post(route('testimonials.store'), $this->data(['profile_id' => $other->id, 'service_id' => $service->id]))->assertSessionHasErrors('profile_id');
        $this->assertDatabaseCount('testimonials', 1);
        $this->post(route('testimonials.store'), $this->data(['profile_id' => [$profile->id], 'service_id' => $service->id]))->assertSessionHasErrors('profile_id');
    }

    public function test_owner_can_view_hidden_profile_and_manage_its_reviews(): void
    {
        $profile = $this->profile();
        $profile->update(['is_visible' => false]);
        $this->get(route('profiles.show', $profile))->assertForbidden();
        $this->actingAs($profile->user)->get(route('profiles.show', $profile))->assertOk()->assertSee('Manage my reviews');
        $this->get(route('profiles.reviews.index', $profile))->assertOk();
    }
}
