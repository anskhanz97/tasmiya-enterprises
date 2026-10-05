<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviews_are_off_by_default_and_make_no_google_request(): void
    {
        Http::fake();

        $this->get('/api/google-reviews')->assertNotFound();
        $this->get('/')->assertOk()->assertDontSee('id="google-reviews"', false);

        Http::assertNothingSent();
    }

    public function test_enabled_reviews_are_fetched_live_without_exposing_the_key(): void
    {
        SiteSetting::set('integration_enable_google_reviews', '1', 'boolean');
        SiteSetting::set('integration_google_reviews_place_id', 'ChIJexample');
        SiteSetting::set('integration_google_reviews_api_key', 'AIzaExampleSecretKey', 'secret');

        Http::fake(['places.googleapis.com/*' => Http::response([
            'displayName' => ['text' => 'Tasmiya Enterprises'],
            'rating' => 4.8,
            'userRatingCount' => 12,
            'googleMapsUri' => 'https://maps.google.com/example',
            'reviews' => [[
                'rating' => 5,
                'text' => ['text' => 'Helpful and professional.'],
                'authorAttribution' => ['displayName' => 'A Client', 'uri' => 'https://maps.google.com/author'],
                'googleMapsUri' => 'https://maps.google.com/review',
            ]],
        ])]);

        $this->get('/')->assertOk()->assertSee('id="google-reviews"', false)->assertDontSee('AIzaExampleSecretKey');
        $this->get('/api/google-reviews')
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertJsonPath('reviews.0.author', 'A Client')
            ->assertJsonPath('reviews.0.url', 'https://maps.google.com/review')
            ->assertDontSee('AIzaExampleSecretKey');
    }
}
