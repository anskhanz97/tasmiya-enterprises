<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileReviewController extends Controller
{
    public function index(Profile $profile)
    {
        $this->authorize('update', $profile);

        return view('profiles.reviews', [
            'profile' => $profile->load('user'),
            'reviews' => Testimonial::forProfile($profile->id)->latest()->paginate(10),
            'services' => $profile->services()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Profile $profile)
    {
        $this->authorize('update', $profile);
        Testimonial::create($this->data($request, $profile));

        return redirect()->route('profiles.reviews.index', $profile)->with('success', 'Review added.');
    }

    public function update(Request $request, Profile $profile, Testimonial $testimonial)
    {
        $this->ownedReview($profile, $testimonial);
        $testimonial->update($this->data($request, $profile));

        return redirect()->route('profiles.reviews.index', $profile)->with('success', 'Review updated.');
    }

    public function destroy(Profile $profile, Testimonial $testimonial)
    {
        $this->ownedReview($profile, $testimonial);
        $testimonial->delete();

        return redirect()->route('profiles.reviews.index', $profile)->with('success', 'Review deleted.');
    }

    private function ownedReview(Profile $profile, Testimonial $testimonial): void
    {
        $this->authorize('update', $profile);
        abort_unless(Testimonial::forProfile($profile->id)->whereKey($testimonial->id)->exists(), 404);
    }

    private function data(Request $request, Profile $profile): array
    {
        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:100'],
            'author_position' => ['nullable', 'string', 'max:100'],
            'author_company' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'min:20', 'max:500'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'service_id' => ['nullable', 'integer', Rule::exists('service_profile', 'service_id')->where('profile_id', $profile->id)],
            'is_approved' => ['required', 'boolean'],
        ]);
        $serviceId = $data['service_id'] ?? null;
        unset($data['service_id']);

        return $data + [
            'profile_id' => $profile->id,
            'testimonialable_type' => $serviceId ? Service::class : Profile::class,
            'testimonialable_id' => $serviceId ?: $profile->id,
        ];
    }
}
