<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class GoogleReviewsController extends Controller
{
    public function index(): JsonResponse
    {
        $placeId = SiteSetting::get('integration_google_reviews_place_id');
        $apiKey = SiteSetting::get('integration_google_reviews_api_key');

        abort_unless(SiteSetting::get('integration_enable_google_reviews') === '1' && $placeId && $apiKey, 404);

        try {
            $response = Http::timeout(6)->withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'displayName,rating,userRatingCount,googleMapsUri,reviews,attributions',
            ])->get('https://places.googleapis.com/v1/places/'.$placeId, ['languageCode' => 'en']);
        } catch (\Throwable $exception) {
            return response()->json(['message' => 'Google reviews are temporarily unavailable.'], 503);
        }

        if (! $response->successful()) {
            return response()->json(['message' => 'Google reviews are temporarily unavailable.'], 503);
        }

        $data = $response->json();

        return response()->json([
            'place' => $data['displayName']['text'] ?? 'Tasmiya Enterprises',
            'rating' => $data['rating'] ?? null,
            'count' => $data['userRatingCount'] ?? 0,
            'maps_url' => $data['googleMapsUri'] ?? null,
            'attributions' => collect($data['attributions'] ?? [])->map(fn ($attribution) => [
                'name' => $attribution['provider'] ?? '',
                'url' => $attribution['providerUri'] ?? null,
            ])->all(),
            'reviews' => collect($data['reviews'] ?? [])->map(fn ($review) => [
                'author' => $review['authorAttribution']['displayName'] ?? 'Google reviewer',
                'author_url' => $review['authorAttribution']['uri'] ?? null,
                'author_photo' => $review['authorAttribution']['photoUri'] ?? null,
                'rating' => $review['rating'] ?? null,
                'text' => $review['text']['text'] ?? $review['originalText']['text'] ?? '',
                'date' => $review['relativePublishTimeDescription'] ?? null,
                'url' => $review['googleMapsUri'] ?? $data['googleMapsUri'] ?? null,
            ])->all(),
        ])->header('Cache-Control', 'no-store, private');
    }
}
