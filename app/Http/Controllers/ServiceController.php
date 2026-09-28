<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceOffering;
use App\Models\Division;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;

/**
 * ServiceController
 *
 * Manages CRUD operations for services offered by Tasmiya Enterprises.
 * Services are offerings that belong to divisions and can be provided by multiple experts.
 *
 * Key Concepts:
 * - Eager Loading: Load related division and testimonials to prevent N+1 queries
 * - Scopes: Use reusable query filters (active(), byDivision())
 * - Authorization: Use policies to check permissions
 *
 * Performance Note:
 * This controller demonstrates proper eager loading patterns.
 *
 * BAD (N+1 Problem):
 * ```php
 * $services = Service::all();
 * foreach ($services as $service) {
 *     echo $service->division->name;      // Extra query per service!
 *     echo $service->getAverageRating();  // Extra query per service!
 * }
 * // Total: 1 + N + N queries = SLOW
 * ```
 *
 * GOOD (Optimized):
 * ```php
 * $services = Service::with(['division', 'testimonials'])
 *     ->active()
 *     ->get();
 * // Total: 3 queries = FAST
 * ```
 */
class ServiceController extends Controller
{
    /**
     * Display a listing of all active services.
     *
     * Shows services grouped by division on the public website.
     * Only displays active (is_active = true) services.
     *
     * Database Optimization:
     * - Eager loads division for each service (prevents N+1)
     * - Eager loads testimonials with only approved reviews
     * - Uses indexing on is_active for fast filtering
     *
     * View Context:
     * - services: Collection of services grouped by division
     * - Each service includes: name, description, image, price, division, rating
     *
     * Example Output:
     * ```
     * FBR Taxation Division
     *   - Tax Consultation (4.8★ from 12 reviews)
     *   - Compliance Audit (4.9★ from 8 reviews)
     *
     * IT & Digital Division
     *   - Web Development (4.7★ from 15 reviews)
     *   - Cloud Solutions (4.8★ from 10 reviews)
     * ```
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // The listing needs counts, not full testimonial/profile collections.
        $services = ServiceOffering::query()
            ->whereHas('service', fn ($query) => $query->active())
            ->whereHas('profile', fn ($query) => $query->visible())
            ->with([
                'service' => fn ($query) => $query->with('division')->withCount([
                    'testimonials as approved_testimonials_count' => fn ($reviews) => $reviews->where('is_approved', true),
                ]),
                'profile.user',
            ])
            ->orderBy('service_id')
            ->get();

        // Group services by division for better organization in view
        $servicesByDivision = $services->groupBy(fn ($offering) => $offering->service->division_id);

        // Calculate totals
        $totalServices = $services->count();
        $totalReviews = $services->pluck('service')->unique('id')->sum(fn ($service) => $service->getTestimonialCount());

        return view('services.index', [
            'servicesByDivision' => $servicesByDivision,
            'totalServices' => $totalServices,
            'totalReviews' => $totalReviews,
        ]);
    }

    /**
     * Display a specific service with details and testimonials.
     *
     * Shows the full service page including:
     * - Service description and pricing
     * - Division information and theming
     * - Profiles (experts) who provide this service
     * - Testimonials/reviews (paginated, approved only)
     * - Rating breakdown chart
     * - "Contact Expert" or "Book Service" CTA
     *
     * Database Optimization:
     * - Eager loads profiles with their division for experts section
     * - Eager loads testimonials (lazy loaded with pagination)
     *
     * Security:
     * - No explicit authorization needed (public viewing)
     * - Only active services are visible
     *
     * Route Parameter:
     * - {service} is auto-resolved by Route Model Binding
     * - Can use ID: /services/1 or slug: /services/tax-consultation
     *
     * @param Service $service Route model binding (auto-fetched from slug)
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        // Ensure service is active (security check)
        if (! $service->is_active) {
            abort(404, 'Service not found');
        }

        // Load related data needed for view
        // Eager load profiles with their division info
        $service->load([
            'profiles' => function ($query) {
                $query->visible()->with(['division', 'user']);
            },
            'division',
        ]);

        // Get paginated testimonials separately (common for large review lists)
        $testimonials = $service->testimonials()
            ->where('is_approved', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // Calculate statistics for display
        $averageRating = $service->getAverageRating();
        $reviewCount = $service->getTestimonialCount();
        $visibleOfferings = $service->offerings()
            ->whereHas('profile', fn ($query) => $query->visible())
            ->with('profile.user')->get();
        $specialistsCount = $visibleOfferings->count();
        $selectedOffering = $visibleOfferings->firstWhere('profile_id', (int) request()->query('profile'));
        $displayOffering = $selectedOffering ?? $visibleOfferings->sortBy(fn ($offering) => (float) $offering->price())->first();

        return view('services.show', [
            'service' => $service,
            'testimonials' => $testimonials,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'specialistsCount' => $specialistsCount,
            'visibleOfferings' => $visibleOfferings,
            'displayOffering' => $displayOffering,
        ]);
    }

    /**
     * Show the form for creating a new service.
     *
     * Only admins can create services.
     *
     * Displays:
     * - Form fields for all service information
     * - Division selector
     * - Profile selector for assigning experts
     *
     * Authorization:
     * - Requires admin privileges (checked via middleware or policy)
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Authorization check
        $this->authorize('create', Service::class);

        // Get all divisions and profiles for form
        $divisions = Division::all();
        $profiles = \App\Models\Profile::with('user')->get();

        return view('services.create', [
            'divisions' => $divisions,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Store a newly created service in database.
     *
     * Validates input using StoreServiceRequest which includes:
     * - Name uniqueness check
     * - Description and long_description validation
     * - Price validation (must be positive number)
     * - Division existence validation
     * - Optional profile IDs for service assignment
     *
     * What happens:
     * 1. Request validates all input
     * 2. Create Service record
     * 3. Attach profiles (many-to-many via pivot table)
     * 4. Redirect with success message
     *
     * Database Operations:
     * - INSERT into services table
     * - INSERT into service_profile pivot table (if profiles provided)
     *
     * @param StoreServiceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreServiceRequest $request)
    {
        // Authorization check (implicit in request)
        $this->authorize('create', Service::class);

        // Create the service with validated data
        // Slug is auto-generated in model's boot() method
        $service = Service::create($request->validated());

        // Attach profiles (experts) if provided
        // attach() adds records to the service_profile pivot table
        if ($request->has('profiles') && ! empty($request->input('profiles'))) {
            $service->profiles()->attach($request->input('profiles'));
        }

        return redirect()
            ->route('services.show', $service)
            ->with('success', "Service '{$service->name}' created successfully!");
    }

    /**
     * Show the form for editing a specific service.
     *
     * Displays the edit form pre-filled with current service data.
     * Only service owner or admin can edit.
     *
     * Loads:
     * - Current service data
     * - All divisions for dropdown
     * - Currently assigned profiles (for multi-select)
     *
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        // Authorization check
        $this->authorize('update', $service);

        // Load currently assigned profiles (for form pre-fill)
        $service->load('profiles');

        // Get all divisions and profiles for dropdown
        $divisions = Division::all();
        $profiles = \App\Models\Profile::with('user')->get();

        return view('services.edit', [
            'service' => $service,
            'divisions' => $divisions,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Update the specified service in database.
     *
     * Validates input using UpdateServiceRequest.
     * Updates service details and manages profile assignments.
     *
     * What happens:
     * 1. Request validates all input
     * 2. Update Service record
     * 3. Sync profiles (replace old assignments with new ones)
     * 4. Redirect with success message
     *
     * Database Operations:
     * - UPDATE services table
     * - DELETE old pivot records and INSERT new ones
     *
     * Security:
     * - Slug cannot be updated (regenerated from name only)
     * - Requires authorization via policy
     *
     * @param UpdateServiceRequest $request
     * @param Service $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        // Authorization check
        $this->authorize('update', $service);

        // Update service details
        $service->update($request->validated());

        // Sync profiles: this replaces all current assignments with the new ones
        // If no profiles provided, all assignments are removed
        // sync() is better than attach() because it removes old records
        if ($request->has('profiles')) {
            $service->profiles()->sync($request->input('profiles', []));
        }

        return redirect()
            ->route('services.show', $service)
            ->with('success', "Service '{$service->name}' updated successfully!");
    }

    /**
     * Delete the specified service.
     *
     * Removes the service from the database.
     * All pivot records (service_profile) are cascade deleted.
     *
     * What happens:
     * 1. Authorization check (admin only)
     * 2. Delete service record
     * 3. Cascade delete all associated pivot records
     * 4. Testimonials remain (different models) - handled separately if needed
     * 5. Redirect with success message
     *
     * Security:
     * - Requires admin privileges
     * - Requires explicit authorization via policy
     *
     * @param Service $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Service $service)
    {
        // Authorization check (admin only)
        $this->authorize('delete', $service);

        $serviceName = $service->name;

        // Delete the service
        // Cascade rules in migration handle deleting pivot records
        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', "Service '{$serviceName}' deleted successfully!");
    }

    /**
     * Search services by name or description.
     *
     * Accepts a 'query' parameter and returns matching services.
     * Useful for autocomplete or search functionality.
     *
     * Search Behavior:
     * - Searches in name and description fields
     * - Case-insensitive (LIKE operator)
     * - Only searches active services
     * - Returns max 10 results for performance
     *
     * Usage:
     * GET /services/search?query=tax
     *
     * Example Response:
     * [
     *   { id: 1, name: "Tax Consultation", rating: 4.8 },
     *   { id: 2, name: "Tax Compliance", rating: 4.5 },
     * ]
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        if (strlen($query) < 2) {
            return response()->json([
                'error' => 'Query must be at least 2 characters',
                'results' => []
            ]);
        }

        $services = Service::query()
            ->active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('division')
            ->limit(10)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'rating' => $service->getAverageRating(),
                    'division' => $service->division->name,
                    'price' => $service->getFormattedPrice(),
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $services->count(),
            'results' => $services
        ]);
    }
}
