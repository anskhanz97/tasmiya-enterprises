<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Service;
use App\Models\Profile;
use App\Http\Requests\StoreTestimonialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TestimonialController
 *
 * Manages testimonial submissions and display.
 *
 * Testimonials use a POLYMORPHIC relationship pattern:
 * - testimonialable_type = 'App\Models\Service' → Review of a service
 * - testimonialable_type = 'App\Models\Profile' → Review of an expert person
 *
 * Approval Workflow:
 * 1. User submits testimonial (is_approved = false)
 * 2. Email notification sent to admin
 * 3. Admin reviews in dashboard
 * 4. Admin approves (is_approved = true)
 * 5. Testimonial appears on website
 *
 * Security Features:
 * - Input validation (content length, rating 1-5)
 * - Rate limiting (prevent spam submissions)
 * - Approval workflow (prevent inappropriate reviews)
 */
class TestimonialController extends Controller
{
    /**
     * Show testimonial submission form.
     *
     * Displays a form for users to submit a review.
     * Can be for a service or profile (passed as query parameter).
     *
     * Usage:
     * /testimonials/create?type=service&id=1  → Review a service
     * /testimonials/create?type=profile&id=3  → Review a profile
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Get query parameters for optional direct linking
        $serviceId = $request->input('service_id');
        $profileId = $request->input('profile_id');
        
        // If service_id is provided, load that service
        $service = null;
        $profile = null;
        
        if ($serviceId) {
            $service = Service::findOrFail($serviceId);
        }
        
        if ($profileId) {
            $profile = Profile::findOrFail($profileId);
        }

        // Get all services and profiles for the selection dropdowns
        $services = Service::active()->with('division')->get();
        $profiles = Profile::visible()->with('user')->get();

        return view('testimonials.create', [
            'service' => $service,
            'profile' => $profile,
            'services' => $services,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Store a new testimonial submission.
     *
     * Creates a testimonial record with:
     * - Submitted review content and rating
     * - Author information
     * - Polymorphic relationship to service/profile
     * - Status: is_approved = false (awaiting admin review)
     *
     * Validation (via StoreTestimonialRequest):
     * - Content: 20-500 characters
     * - Rating: Integer 1-5
     * - Author name: Required, max 100 chars
     * - Author position: Optional
     * - Author company: Optional
     * - Must link to either service OR profile (or both)
     *
     * What happens:
     * 1. Request validates input
     * 2. Create testimonial with is_approved = false
     * 3. Send email to admin for review
     * 4. Return to referring page with success message
     *
     * Polymorphic Example:
     * ```php
     * // Create testimonial for a service
     * Testimonial::create([
     *     'content' => 'Great service!',
     *     'rating' => 5,
     *     'author_name' => 'John Doe',
     *     'testimonialable_type' => Service::class,  // Full class name
     *     'testimonialable_id' => 1,
     *     'is_approved' => false,
     * ]);
     * ```
     *
     * @param StoreTestimonialRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTestimonialRequest $request)
    {
        $validated = $request->validated();

        // Get service_id and profile_id from request
        $serviceId = $request->input('service_id');
        $profileId = $request->input('profile_id');

        // Determine what this testimonial is for (must be at least one)
        if ($serviceId) {
            $validated['testimonialable_type'] = Service::class;
            $validated['testimonialable_id'] = $serviceId;
        } elseif ($profileId) {
            $validated['testimonialable_type'] = Profile::class;
            $validated['testimonialable_id'] = $profileId;
        } else {
            // This shouldn't happen if validation worked properly
            return back()->with('error', 'Please select a service or team member to review.');
        }

        // Create testimonial (starts as unapproved)
        $testimonial = Testimonial::create($validated);

        // TODO: Send email to admin for review notification
        // Mail::to(config('mail.admin_email'))
        //     ->send(new TestimonialSubmitted($testimonial));

        // Redirect back to service if provided, otherwise to services index
        $redirectUrl = $serviceId 
            ? route('services.show', Service::find($serviceId))
            : route('services.index');

        return redirect($redirectUrl)
            ->with('success', 'Thank you! Your review has been submitted and will appear after admin approval.');
    }

    /**
     * Display a single testimonial.
     *
     * Shows full testimonial details.
     * Useful for admin review or detail pages.
     *
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\Response
     */
    public function show(Testimonial $testimonial)
    {
        // Check if approved (or user is admin)
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (! $testimonial->is_approved && ! $authUser?->isAdmin()) {
            abort(403, 'This testimonial is not available');
        }

        return view('testimonials.show', [
            'testimonial' => $testimonial,
        ]);
    }

    /**
     * Admin: Approve a testimonial for public display.
     *
     * Changes is_approved from false to true.
     * Testimonial becomes visible on service/profile pages.
     *
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Testimonial $testimonial)
    {
        // Admin authorization check
        $this->authorize('approve', $testimonial);

        $testimonial->approve();

        return back()->with('success', 'Testimonial approved!');
    }

    /**
     * Admin: Reject a testimonial (don't show publicly).
     *
     * Changes is_approved to false.
     * Testimonial stays in database but doesn't display.
     * Can be reviewed again later.
     *
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Testimonial $testimonial)
    {
        // Admin authorization check
        $this->authorize('reject', $testimonial);

        $testimonial->reject();

        return back()->with('success', 'Testimonial rejected.');
    }

    /**
     * Admin: Mark testimonial as featured.
     *
     * Featured testimonials display first and are highlighted.
     * Useful for excellent or representative reviews.
     *
     * Example Usage:
     * In service detail view, featured testimonials appear above others.
     *
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function feature(Testimonial $testimonial)
    {
        // Admin authorization check
        $this->authorize('feature', $testimonial);

        $testimonial->markFeatured();

        return back()->with('success', 'Testimonial marked as featured!');
    }

    /**
     * Admin: Unmark testimonial as featured.
     *
     * Remove featured status to show in normal order.
     *
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unfeature(Testimonial $testimonial)
    {
        // Admin authorization check
        $this->authorize('feature', $testimonial);

        $testimonial->unmarkFeatured();

        return back()->with('success', 'Featured status removed.');
    }

    /**
     * Admin: Delete a testimonial.
     *
     * Permanently removes testimonial from database.
     * Cannot be undone.
     *
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Testimonial $testimonial)
    {
        // Admin authorization check
        $this->authorize('delete', $testimonial);

        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted.');
    }

    /**
     * Get testimonials for a service (API endpoint).
     *
     * Returns approved testimonials in JSON format.
     * Used for loading reviews dynamically on service pages.
     *
     * Parameters:
     * - service_id: Required
     * - page: Optional (default 1)
     * - per_page: Optional (default 5)
     * - sort: Optional ('recent' or 'rating')
     *
     * Example Response:
     * ```json
     * {
     *   "success": true,
     *   "count": 3,
     *   "testimonials": [
     *     {
     *       "id": 1,
     *       "content": "Excellent service!",
     *       "rating": 5,
     *       "author_name": "John Doe",
     *       "author_info": "CEO at Acme Corp",
     *       "avatar_url": "https://...",
     *       "created_at": "2026-02-05"
     *     }
     *   ]
     * }
     * ```
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForService(Request $request)
    {
        $serviceId = $request->input('service_id');
        $perPage = $request->input('per_page', 5);
        $sort = $request->input('sort', 'recent');

        if (! $serviceId) {
            return response()->json([
                'error' => 'service_id is required',
                'testimonials' => []
            ], 422);
        }

        $query = Testimonial::forService($serviceId)
            ->approved();

        // Apply sorting
        if ($sort === 'rating') {
            $query->orderBy('rating', 'desc');
        } else {
            $query->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $testimonials = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'count' => $testimonials->total(),
            'testimonials' => $testimonials->items(),
            'pagination' => [
                'total' => $testimonials->total(),
                'per_page' => $testimonials->perPage(),
                'current_page' => $testimonials->currentPage(),
                'last_page' => $testimonials->lastPage(),
            ]
        ]);
    }

    /**
     * Get testimonials for a profile (API endpoint).
     *
     * Returns approved testimonials in JSON format.
     * Used for loading reviews dynamically on profile pages.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getForProfile(Request $request)
    {
        $profileId = $request->input('profile_id');
        $perPage = $request->input('per_page', 5);
        $sort = $request->input('sort', 'recent');

        if (! $profileId) {
            return response()->json([
                'error' => 'profile_id is required',
                'testimonials' => []
            ], 422);
        }

        $query = Testimonial::forProfile($profileId)
            ->approved();

        // Apply sorting
        if ($sort === 'rating') {
            $query->orderBy('rating', 'desc');
        } else {
            $query->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $testimonials = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'count' => $testimonials->total(),
            'testimonials' => $testimonials->items(),
        ]);
    }
}
