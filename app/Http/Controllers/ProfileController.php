<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * ProfileController - Manage user profiles and team showcase
 * 
 * Purpose:
 * This controller handles all profile-related operations:
 * - Viewing public profiles (team showcase)
 * - Editing user profiles
 * - Managing profile visibility
 * 
 * Routes handled:
 * - GET /profile - List all visible profiles (team showcase)
 * - GET /profile/{profile} - View specific profile
 * - GET /profile/{profile}/edit - Show edit form
 * - PUT /profile/{profile} - Update profile
 * 
 * Authorization:
 * - View: Public (anyone can view)
 * - Edit: Owner or admin only
 * - Delete: Admin only
 * 
 * Key patterns used:
 * - Eager loading: with(['user', 'division']) to prevent N+1 queries
 * - Policy authorization: $this->authorize('action', $resource)
 * - Form request validation: ProfileUpdateRequest
 * - Resource routing: RESTful endpoints
 */
class ProfileController extends Controller
{
    /**
     * Display team showcase - List all visible profiles
     * 
     * HTTP Method: GET
     * Route: /profiles
     * Middleware: none (public)
     * 
     * Purpose:
     * Shows a grid/list of all team members with their profiles visible
     * This is the main team showcase page displayed to visitors
     * 
     * Process:
     * 1. Query all visible profiles
     * 2. Eager load user and division data (prevents N+1 problem)
     * 3. Optionally group by division for better UX
     * 4. Pass to view with theme information
     * 
     * Database Query:
     * SELECT * FROM profiles
     * WHERE is_visible = 1
     * WITH users, divisions
     * 
     * N+1 Problem Explained:
     * Without eager loading:
     *   1 query: SELECT * FROM profiles WHERE is_visible = 1 (gets 4 profiles)
     *   4 queries: SELECT * FROM users WHERE id = ? (1 for each profile)
     *   4 queries: SELECT * FROM divisions WHERE id = ? (1 for each user)
     *   Total: 9 queries (BAD for performance)
     * 
     * With eager loading:
     *   1 query: SELECT * FROM profiles WHERE is_visible = 1
     *   1 query: SELECT * FROM users WHERE id IN (...)
     *   1 query: SELECT * FROM divisions WHERE id IN (...)
     *   Total: 3 queries (GOOD for performance)
     * 
     * @return View - profiles.index view with profile collection
     */
    public function index(): View
    {
        // Eager load user and division to prevent N+1 queries
        // with() tells Laravel to load these relationships in one query
        $profiles = Profile::with(['user', 'division'])
            ->visible() // Use scope: where('is_visible', true)
            ->orderBy('created_at', 'desc') // Newest first
            ->get();

        // Group profiles by division for better organization (optional)
        // This allows showing profiles grouped by their division
        $profilesByDivision = $profiles->groupBy(function ($profile) {
            return $profile->user->division_id;
        });

        return view('profiles.index', [
            'profiles' => $profiles,
            'profilesByDivision' => $profilesByDivision,
        ]);
    }

    /**
     * Display team showcase - List all visible profiles (team view)
     * Maps to /team route
     */
    public function team(): View
    {
        $profiles = Profile::with(['user.division'])
            ->visible()
            ->orderBy('created_at', 'asc')
            ->get();

        return view('team.index', ['profiles' => $profiles]);
    }

    /**
     * Display a specific user profile (public view)
     * 
     * HTTP Method: GET
     * Route: /profile/{profile}
     * Middleware: none (public)
     * 
     * Purpose:
     * Shows a single profile with full details
     * Can be visited by anyone (profile is public)
     * 
     * Process:
     * 1. Laravel automatically finds profile by route parameter
     * 2. Check if profile is visible (403 if hidden and not admin)
     * 3. Eager load relationships for efficient queries
     * 4. Get theme colors from division for styling
     * 5. Render profile view with all data
     * 
     * Route Model Binding:
     * Route parameter {profile} is automatically resolved to Profile model
     * Example: /profile/1 automatically calls Profile::find(1)
     * If not found, Laravel returns 404 automatically
     * 
     * Authorization Example:
     * - Visitor sees: Public profile with limited info
     * - Owner sees: Full profile with edit button
     * - Admin sees: Full profile with edit/delete buttons
     * 
     * @param Profile $profile - Automatically bound from route parameter
     * @return View - profiles.show view with profile details and theme
     */
    public function show(Profile $profile): View
    {
        // Verify profile is visible or user is admin
        // This prevents showing hidden profiles to regular users
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (! $profile->is_visible && ! $authUser?->isAdmin()) {
            abort(403, 'This profile is not publicly visible');
        }

        // Eager load relationships if not already loaded
        // (Route model binding may have already loaded them, but be safe)
        if (! $profile->relationLoaded('user')) {
            $profile->load(['user', 'division']);
        }
        $profile->load(['serviceOfferings' => fn ($query) => $query->whereHas('service', fn ($service) => $service->active())->with(['service.division', 'profile.user'])]);

        // Get theme colors from profile's division
        // Used in view to apply division-specific styling
        $theme = $profile->getThemeColors();

        // Get the division brand info for displaying division info
        $division = $profile->user->division;

        // Get WhatsApp URL if available (Phase 5 integration)
        $whatsappUrl = $profile->getWhatsAppUrl(
            "Hi {$profile->user->name}, I'd like to know more about your services."
        );

        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        return view('profiles.show', [
            'profile' => $profile,
            'theme' => $theme,
            'division' => $division,
            'whatsappUrl' => $whatsappUrl,
            'canEdit' => $authUser && $authUser->can('update', $profile),
            'canDelete' => $authUser && $authUser->can('delete', $profile),
        ]);
    }

    /**
     * Show the edit form for a profile
     * 
     * HTTP Method: GET
     * Route: /profile/{profile}/edit
     * Middleware: auth (must be logged in)
     * 
     * Purpose:
     * Display form for updating a profile
     * Authorization is checked: only owner or admin can access
     * 
     * Authorization Check:
     * Uses ProfilePolicy@update to verify user can edit this profile
     * If not authorized, returns 403 Forbidden
     * 
     * The $this->authorize() method:
     * - Checks the policy rule (ProfilePolicy@update)
     * - Passes authenticated user and profile to policy
     * - Throws AuthorizationException (403) if denied
     * - Otherwise continues
     * 
     * Example flow:
     * User (id=1) tries to edit profile (user_id=2)
     * → ProfilePolicy@update(User:1, Profile:2) → false
     * → 403 Forbidden error thrown
     * 
     * @param Profile $profile - Profile to edit
     * @return View - profiles.edit form view
     * @throws \Illuminate\Auth\Access\AuthorizationException - If unauthorized
     */
    public function edit(Profile $profile): View
    {
        // Check if user can edit this profile
        // This uses the ProfilePolicy@update method
        // If not authorized, throws 403 Forbidden
        $this->authorize('update', $profile);

        // Get theme for consistent styling
        $theme = $profile->getThemeColors();

        // Eager load user and division
        $profile->load(['user', 'division']);

        return view('profiles.edit', [
            'profile' => $profile,
            'theme' => $theme,
            'division' => $profile->user->division,
        ]);
    }

    /**
     * Update a profile
     * 
     * HTTP Method: PUT/PATCH
     * Route: /profile/{profile}
     * Middleware: auth (must be logged in)
     * 
     * Purpose:
     * Save profile changes to database including file uploads
     * Authorization and validation handled before this method
     * 
     * Handles both regular fields and file uploads for profile/banner images
     * 
     * @param ProfileUpdateRequest $request - Validated input data
     * @param Profile $profile - Profile to update
     * @return RedirectResponse - Redirect to profile view
     * @throws \Illuminate\Auth\Access\AuthorizationException - If unauthorized
     */
    public function update(ProfileUpdateRequest $request, Profile $profile): RedirectResponse
    {
        // ProfileUpdateRequest already validated and authorized
        // $request->validated() returns only safe, validated data
        $validated = $request->validated();
        $order = explode(',', $validated['section_order']);
        $validated['presentation_sections'] = collect($order)->map(fn ($key, $index) => [
            'key' => $key,
            'title' => trim($validated['section_titles'][$key] ?? Profile::SECTION_TITLES[$key]),
            'description' => trim($validated['section_descriptions'][$key] ?? ''),
            'visible' => (bool) ($validated['section_visibility'][$key] ?? true),
            'order' => $index,
        ])->all();
        unset($validated['section_order'], $validated['section_titles'], $validated['section_descriptions'], $validated['section_visibility']);

        $validated['specializations'] = array_values(array_filter(
            array_map('trim', $validated['specializations'] ?? []),
            fn ($value) => $value !== ''
        ));
        $validated['social_links'] = array_merge($profile->social_links ?? [], $validated['social_links'] ?? []);

        foreach (['profile', 'banner'] as $kind) {
            $urlKey = "{$kind}_image_url";
            $hideKey = "hide_{$kind}_image";
            $removeKey = "remove_{$kind}_image";
            $replacementUrl = $validated[$urlKey] ?? null;

            if (! $replacementUrl) {
                unset($validated[$urlKey]);
            }

            if ($request->boolean($removeKey)) {
                $this->deleteStoredImage($profile->{$urlKey});
                $validated[$urlKey] = null;
                $validated[$hideKey] = true;
            } elseif ($replacementUrl) {
                $this->deleteStoredImage($profile->{$urlKey});
                $validated[$hideKey] = false;
            }

            if ($request->hasFile("{$kind}_image")) {
                $this->deleteStoredImage($profile->{$urlKey});
                $path = $request->file("{$kind}_image")->store("{$kind}-images", 'public');
                $validated[$urlKey] = '/storage/' . $path;
                $validated[$hideKey] = false;
            }
        }

        unset($validated['profile_image'], $validated['banner_image'], $validated['remove_profile_image'], $validated['remove_banner_image']);
        $profile->update($validated);

        // Redirect to profile view with success message
        return redirect()
            ->route('profiles.show', $profile)
            ->with('success', 'Profile updated successfully! Your changes are now live.');
    }

    private function deleteStoredImage(?string $url): void
    {
        if ($url && str_starts_with($url, '/storage/')) {
            Storage::disk('public')->delete(substr($url, strlen('/storage/')));
        }
    }

    /**
     * Delete a profile
     * 
     * HTTP Method: DELETE
     * Route: /profile/{profile}
     * Middleware: auth (must be logged in)
     * 
     * Purpose:
     * Delete a profile from the database
     * Only admin can delete (checked via policy)
     * 
     * Authorization:
     * Only super_admin or admin with special permission can delete
     * Uses ProfilePolicy@delete method
     * 
     * Note:
     * Deleting a profile does NOT delete the user account
     * It just hides the user from the team showcase
     * Alternative: Set is_visible = false for soft deletion
     * 
     * @param Profile $profile - Profile to delete
     * @return RedirectResponse - Redirect to team showcase
     * @throws \Illuminate\Auth\Access\AuthorizationException - If not admin
     */
    public function destroy(Profile $profile): RedirectResponse
    {
        // Check if user can delete this profile
        // Only admins can delete
        $this->authorize('delete', $profile);

        // Store the user ID before deletion (for redirect)
        $userName = $profile->user->name;

        // Delete the profile
        $profile->delete();

        // Redirect with success message
        return redirect()
            ->route('profiles.index')
            ->with('success', "{$userName}'s profile has been deleted.");
    }
}
