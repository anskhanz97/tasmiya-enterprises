<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Service;
use App\Models\ServiceOffering;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileServiceOfferingController extends Controller
{
    public function index(Profile $profile): View
    {
        $this->authorize('update', $profile);
        $profile->load('user.division');

        return view('profiles.services', [
            'profile' => $profile,
            'offerings' => $profile->serviceOfferings()->with('service.division')->orderBy('id')->get(),
            'availableServices' => Service::query()->active()
                ->where('division_id', $profile->user->division_id)
                ->where(fn ($query) => $query->whereNull('created_by_profile_id')->orWhere('created_by_profile_id', $profile->id))
                ->whereDoesntHave('offerings', fn ($query) => $query->where('profile_id', $profile->id))
                ->orderBy('name')->get(),
            'icons' => config('service_cards.icons'),
        ]);
    }

    public function store(Request $request, Profile $profile): RedirectResponse
    {
        $this->authorize('update', $profile);
        $profile->load('user');
        abort_unless($profile->user->division_id, 422, 'Assign a division to this profile before adding a service.');

        $data = $request->validate([
            'source' => ['required', Rule::in(['existing', 'new'])],
            'service_id' => ['required_if:source,existing', 'nullable', 'integer', 'exists:services,id'],
            'card_title' => ['required_if:source,new', 'nullable', 'string', 'max:100'],
            'card_description' => ['required_if:source,new', 'nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'currency' => ['required', Rule::in(['PKR', 'USD', 'EUR', 'GBP'])],
            'icon_key' => ['required', Rule::in(array_keys(config('service_cards.icons')))],
            'tags' => ['nullable', 'string', 'max:200'],
        ]);

        DB::transaction(function () use ($data, $profile) {
            if ($data['source'] === 'existing') {
                $service = Service::query()->active()
                    ->where('division_id', $profile->user->division_id)
                    ->where(fn ($query) => $query->whereNull('created_by_profile_id')->orWhere('created_by_profile_id', $profile->id))
                    ->findOrFail($data['service_id']);

                if (ServiceOffering::where('profile_id', $profile->id)->where('service_id', $service->id)->exists()) {
                    abort(409, 'This service is already on the profile.');
                }
            } else {
                $title = trim($data['card_title']);
                $description = trim($data['card_description']);
                $service = Service::create([
                    'name' => $title,
                    'slug' => Str::limit(Str::slug($title), 72, '') . '-' . $profile->id . '-' . Str::lower(Str::random(8)),
                    'description' => $description,
                    'base_price' => $data['price'],
                    'currency' => $data['currency'],
                    'division_id' => $profile->user->division_id,
                    'created_by_profile_id' => $profile->id,
                    'is_active' => true,
                ]);
            }

            ServiceOffering::create([
                'profile_id' => $profile->id,
                'service_id' => $service->id,
                'card_title' => $data['source'] === 'new' ? trim($data['card_title']) : null,
                'card_description' => $data['source'] === 'new' ? trim($data['card_description']) : null,
                'price' => $data['price'],
                'currency' => $data['currency'],
                'icon_key' => $data['icon_key'],
                'tags' => $this->parseTags($data['tags'] ?? ''),
            ]);
        });

        return redirect()->route('profiles.services.index', $profile)->with('success', 'Service card added.');
    }

    public function update(Request $request, Profile $profile, ServiceOffering $offering): RedirectResponse
    {
        $this->authorize('update', $profile);
        abort_unless($offering->profile_id === $profile->id, 404);

        $data = $request->validate([
            'card_title' => ['required', 'string', 'max:100'],
            'card_description' => ['required', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'currency' => ['required', Rule::in(['PKR', 'USD', 'EUR', 'GBP'])],
            'icon_key' => ['required', Rule::in(array_keys(config('service_cards.icons')))],
            'tags' => ['nullable', 'string', 'max:200'],
        ]);

        $offering->update([
            'card_title' => trim($data['card_title']),
            'card_description' => trim($data['card_description']),
            'price' => $data['price'],
            'currency' => $data['currency'],
            'icon_key' => $data['icon_key'],
            'tags' => $this->parseTags($data['tags'] ?? ''),
        ]);

        return redirect()->route('profiles.services.index', $profile)->with('success', 'Service card updated.');
    }

    public function destroy(Profile $profile, ServiceOffering $offering): RedirectResponse
    {
        $this->authorize('update', $profile);
        abort_unless($offering->profile_id === $profile->id, 404);

        DB::transaction(function () use ($offering, $profile) {
            $service = $offering->service;
            $offering->delete();

            if ($service->created_by_profile_id === $profile->id && ! $service->offerings()->exists()) {
                $service->update(['is_active' => false]);
            }
        });

        return redirect()->route('profiles.services.index', $profile)->with('success', 'Service card removed.');
    }

    private function parseTags(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($tag) => Str::limit(trim($tag), 32, ''))
            ->filter()->unique(fn ($tag) => Str::lower($tag))->take(4)->values()->all();
    }
}
