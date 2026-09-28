<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileServiceOfferingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Support\Facades\Route;

/**
 * ========== PUBLIC ROUTES ==========
 * Accessible to everyone (guest and authenticated users)
 */

/**
 * Home page
 * GET /
 * Shows welcome/home page with company overview and divisions
 */
Route::get('/', function () {
    $divisions = \App\Models\Division::all();
    $services = \App\Models\ServiceOffering::query()
        ->whereHas('service', fn ($query) => $query->active())
        ->whereHas('profile', fn ($query) => $query->visible())
        ->with(['service.division', 'profile.user'])->get()->shuffle();
    $featuredServices = collect([
        $services->first(fn ($offering) => $offering->service->division?->slug === 'fbr-taxation'),
        $services->first(fn ($offering) => $offering->service->division?->slug === 'tech-support'),
    ])->merge($services->filter(fn ($offering) => $offering->service->division?->slug === 'it-digital')->take(2))->filter();
    $featuredTeam = \App\Models\Profile::with('user.division')->visible()->limit(6)->get();

    return view('home', compact('divisions', 'featuredServices', 'featuredTeam'));
})->name('home');

/**
 * Static Pages
 * GET /about - About Us page
 * GET /privacy - Privacy Policy
 * GET /terms - Terms of Service
 */
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

/**
 * ========== AUTHENTICATION ROUTES ==========
 * Middleware 'guest': Only non-authenticated users can access
 * These are HIDDEN routes (no public links, only admin shares URL)
 * 
 * Security rationale:
 * - No public sign-up for random users
 * - Admin controls who gets registration access
 * - Prevents bot registrations and spam accounts
 * - Still secure (password + email verification required)
 */

/**
 * Show login form
 * GET /login
 * User sees: Email + password form
 */
Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

/**
 * Process login form
 * POST /login
 * User submits: Email + password
 * Server checks: Email exists + password matches
 * Result: Creates session + redirects to dashboard
 * 
 * Middleware:
 * - 'guest': Already logged-in users redirected to dashboard
 * - 'throttle:5,1': Max 5 login attempts per minute per IP
 *   (Prevents brute force attacks)
 */
Route::post('/login', [AuthController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('login.store');

/**
 * Show registration form
 * GET /register
 * User sees: Name + email + password + role + division form
 * 
 * HIDDEN ROUTE:
 * - No link in navigation
 * - Admin must share direct URL with new team members
 * - Example: admin sends email with link to /register
 * - Only the person with the link can register
 * 
 * Could add: Registration token verification
 * To be even more secure: Each registration link is one-time-use
 * For now: Just hidden URL is sufficient for small team
 */
Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest')
    ->name('register');

/**
 * Process registration form
 * POST /register
 * User submits: Name + email + password + role + division
 * Server checks: All fields valid + email unique + password strong
 * Result: Creates user account + fires Registered event (email verification)
 * 
 * Post-registration:
 * - User must verify email (click link in email)
 * - Admin must activate account (sets is_active = true)
 * - Only then can user log in
 */
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register.store');

/**
 * ========== AUTHENTICATED ROUTES ==========
 * Middleware 'auth': Only logged-in users can access
 * Non-authenticated users redirected to /login
 */

/**
 * Logout user
 * POST /logout
 * Destroys session + clears cookies + redirects to home
 * 
 * Why POST not GET:
 * - State-changing operation should use POST
 * - Prevents accidental logouts from link clicks
 * - Requires CSRF token (prevents logout via malicious images)
 * 
 * Usage in view:
 *   <form action="{{ route('logout') }}" method="POST">
 *       @csrf
 *       <button type="submit">Log Out</button>
 *   </form>
 */
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/**
 * Change Password Routes
 * GET /password/change - Show change password form
 * PUT /password/update - Process password change
 */
Route::get('/password/change', [AuthController::class, 'showChangePassword'])
    ->middleware('auth')
    ->name('password.change');

Route::put('/password/update', [AuthController::class, 'updatePassword'])
    ->middleware('auth')
    ->name('password.update');

/**
 * Dashboard (placeholder)
 * GET /dashboard
 * User sees: Their main page after login
 * Shows: Quick stats, recent activity, navigation
 * 
 * Will be replaced in Phase 3 with full admin dashboard
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

/**
 * Site Settings Management
 * GET /settings - View settings page
 * PUT /settings/social - Update social media links
 * Middleware 'auth': Only authenticated users (admins) can access
 */
Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])
    ->middleware('auth')
    ->name('settings.index');

Route::put('/settings/social', [\App\Http\Controllers\SettingsController::class, 'updateSocialLinks'])
    ->middleware('auth')
    ->name('settings.social.update');
Route::put('/settings/integrations', [\App\Http\Controllers\SettingsController::class, 'updateIntegrations'])
    ->middleware('auth')
    ->name('settings.integrations.update');
Route::get('/settings/whatsapp/templates', [\App\Http\Controllers\SettingsController::class, 'whatsappTemplates'])
    ->middleware('auth')
    ->name('settings.whatsapp.templates');
Route::post('/settings/google/verify', [\App\Http\Controllers\SettingsController::class, 'verifyGooglePicker'])
    ->middleware('auth')
    ->name('settings.google.verify');

/**
 * ========== PROFILE ROUTES ==========
 * Routes for viewing and managing user profiles
 * 
 * Resource routing creates standard RESTful routes automatically:
 * - GET /profiles (index) - List all profiles
 * - GET /profiles/{profile} (show) - View one profile
 * - GET /profiles/{profile}/edit (edit) - Show edit form
 * - PUT /profiles/{profile} (update) - Save changes
 * - DELETE /profiles/{profile} (destroy) - Delete profile
 * 
 * Route model binding:
 * {profile} parameter automatically resolved to Profile model
 * Example: /profiles/1 → Profile::find(1)
 * If not found, Laravel returns 404 automatically
 * 
 * Authorization:
 * - Index/Show: Public (everyone can view team showcase)
 * - Edit/Update: Auth middleware (must be logged in)
 *               Policy check (owner or admin only)
 * - Delete: Auth middleware + Policy check (admin only)
 */

/**
 * Team showcase - List all visible profiles
 * GET /profiles
 * Public route - anyone can view team showcase grid
 */
Route::get('/profiles', [ProfileController::class, 'index'])
    ->name('profiles.index');

/**
 * Show public profile page
 * GET /profiles/{profile}
 * Public route - displays profile with division-specific theme
 */
Route::get('/profiles/{profile}', [ProfileController::class, 'show'])
    ->name('profiles.show');

/**
 * Show edit form for profile
 * GET /profiles/{profile}/edit
 * Authenticated route - owner or admin only (checked in controller via policy)
 * 
 * Middleware: auth ensures user is logged in
 * Policy check: Owner or admin (in ProfilePolicy@update)
 * If unauthorized: 403 Forbidden thrown
 */
Route::get('/profiles/{profile}/edit', [ProfileController::class, 'edit'])
    ->middleware('auth')
    ->name('profiles.edit');

/**
 * Update profile
 * PUT /profiles/{profile}
 * Authenticated route - accepts validated profile data
 * 
 * Process:
 * 1. ProfileUpdateRequest validates input
 * 2. ProfileUpdateRequest checks authorization
 * 3. ProfileController@update saves changes
 * 4. Redirect to profile view with success message
 * 
 * Security:
 * - CSRF token required (included in form)
 * - Validation ensures data integrity
 * - Authorization prevents unauthorized access
 * - Only fillable fields can be mass-assigned
 */
Route::put('/profiles/{profile}', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profiles.update');

Route::middleware('auth')->group(function () {
    Route::get('/profiles/{profile}/services', [ProfileServiceOfferingController::class, 'index'])->name('profiles.services.index');
    Route::post('/profiles/{profile}/services', [ProfileServiceOfferingController::class, 'store'])->name('profiles.services.store');
    Route::put('/profiles/{profile}/services/{offering}', [ProfileServiceOfferingController::class, 'update'])->name('profiles.services.update');
    Route::delete('/profiles/{profile}/services/{offering}', [ProfileServiceOfferingController::class, 'destroy'])->name('profiles.services.destroy');
});

/**
 * Delete profile
 * DELETE /profiles/{profile}
 * Admin route - only admins can delete profiles
 * 
 * Note: Deletes profile but not user account
 * User still exists in system (can be re-profiled)
 * 
 * Alternative approach: Set is_visible = false (soft delete)
 * Not implemented yet, but planned for future
 */
Route::delete('/profiles/{profile}', [ProfileController::class, 'destroy'])
    ->middleware('auth')
    ->name('profiles.destroy');

/**
 * ========== TEAM/PROFILE ROUTES ==========
 * Routes for viewing team members and profiles
 */

/**
 * Team showcase - List all visible profiles
 * GET /team
 * Public route - team members organized by division
 */
Route::get('/team', [ProfileController::class, 'team'])
    ->name('team.index');

/**
 * Show public profile page
 * GET /team/{profile}
 * Public route - displays profile with portfolio, services, and testimonials
 */
Route::get('/team/{profile}', [ProfileController::class, 'show'])
    ->name('team.show');

/**
 * Legacy /profiles routes (redirect to /team)
 */
Route::get('/profiles', [ProfileController::class, 'team'])
    ->name('profiles.index');

Route::get('/profiles/{profile}', [ProfileController::class, 'show'])
    ->name('profiles.show');

/**
 * ========== SERVICES PROFILES ROUTES ==========
 * Services are the offerings/products provided by Tasmiya Enterprises
 * Public can view, Admin can create/edit/delete
 */

/**
 * List all services - Team service showcase
 * GET /services
 * Public route - shows all active services grouped by division
 * 
 * Example displays:
 * FBR Taxation Division
 *   - Tax Consultation (4.8★ from 12 reviews)
 *   - Compliance Audit (4.9★ from 8 reviews)
 * 
 * IT & Digital Division
 *   - Web Development (4.7★ from 15 reviews)
 *   - Cloud Solutions (4.8★ from 10 reviews)
 */
Route::get('/services', [ServiceController::class, 'index'])
    ->name('services.index');

// Register the literal path before /services/{service} so "create" is not
// interpreted as a service identifier.
Route::get('/services/create', [ServiceController::class, 'create'])
    ->middleware('auth')
    ->name('services.create');

/**
 * Show single service with details and testimonials
 * GET /services/{service}
 * Public route - displays service with:
 *   - Full description
 *   - Pricing
 *   - List of experts who provide it
 *   - Client testimonials and ratings
 *   - Rating breakdown chart
 * 
 * Route model binding: {service} auto-resolved by slug
 * Example: /services/tax-consultation resolves to Service with slug='tax-consultation'
 */
Route::get('/services/{service}', [ServiceController::class, 'show'])
    ->name('services.show');

/**
 * Create new service form (Admin only)
 * GET /services/create
 * Admin route - shows form to create new service
 * 
 * Authorization: Must be admin (checked in controller via policy)
 */
/**
 * Store new service (Admin only)
 * POST /services
 * Admin route - accepts validated service data
 * 
 * Process:
 * 1. StoreServiceRequest validates input
 * 2. StoreServiceRequest checks authorization
 * 3. ServiceController@store creates service
 * 4. Attach profiles (experts) via pivot table
 * 5. Redirect to service view with success message
 */
Route::post('/services', [ServiceController::class, 'store'])
    ->middleware('auth')
    ->name('services.store');

/**
 * Edit service form (Admin only)
 * GET /services/{service}/edit
 * Admin route - shows form pre-filled with current service data
 * 
 * Authorization: Must be admin
 */
Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])
    ->middleware('auth')
    ->name('services.edit');

/**
 * Update service (Admin only)
 * PUT /services/{service}
 * Admin route - accepts validated updated service data
 * 
 * Process:
 * 1. UpdateServiceRequest validates input
 * 2. ServiceController@update saves changes
 * 3. Sync profiles (replaces old assignments with new ones)
 * 4. Redirect to service view
 */
Route::put('/services/{service}', [ServiceController::class, 'update'])
    ->middleware('auth')
    ->name('services.update');

/**
 * Delete service (Admin only)
 * DELETE /services/{service}
 * Admin route - permanently removes service
 * 
 * Cascade deletes: All associated pivot records and testimonials
 */
Route::delete('/services/{service}', [ServiceController::class, 'destroy'])
    ->middleware('auth')
    ->name('services.destroy');

/**
 * Search services API endpoint
 * GET /services/search
 * Public API endpoint - search services by name/description
 * 
 * Parameters:
 * - query: Search string (min 2 chars)
 * 
 * Returns JSON with matching services
 */
Route::get('/api/services/search', [ServiceController::class, 'search'])
    ->name('services.search');

/**
 * ========== TESTIMONIALS ROUTES (PHASE 4) ==========
 * Testimonials are client reviews/feedback
 * Public can submit, Admin can manage (approve/feature/delete)
 */

/**
 * Show testimonial submission form
 * GET /testimonials/create
 * Public route - shows form to submit review
 * 
 * Query parameters:
 * - type: 'service' or 'profile' (what is being reviewed)
 * - id: ID of service or profile being reviewed
 * 
 * Example: /testimonials/create?type=service&id=1
 */
Route::get('/testimonials/create', [TestimonialController::class, 'create'])
    ->name('testimonials.create');

/**
 * Store new testimonial
 * POST /testimonials
 * Public route - accepts validated review data
 * 
 * Process:
 * 1. StoreTestimonialRequest validates input
 * 2. TestimonialController@store creates testimonial
 * 3. Set is_approved = false (awaiting admin approval)
 * 4. Redirect to referring page with "Thank you" message
 * 
 * Workflow:
 * - User submits → is_approved = false
 * - Email notification sent to admin
 * - Admin reviews in dashboard
 * - Admin approves → is_approved = true
 * - Testimonial appears on website
 */
Route::post('/testimonials', [TestimonialController::class, 'store'])
    ->name('testimonials.store');

/**
 * Show single testimonial
 * GET /testimonials/{testimonial}
 * Public route - only approved testimonials visible to public
 * Admin can see all testimonials (including pending)
 */
Route::get('/testimonials/{testimonial}', [TestimonialController::class, 'show'])
    ->name('testimonials.show');

/**
 * Admin: Approve testimonial
 * POST /testimonials/{testimonial}/approve
 * Admin route - mark testimonial as approved for public display
 * 
 * Sets is_approved = true
 */
Route::post('/testimonials/{testimonial}/approve', [TestimonialController::class, 'approve'])
    ->middleware('auth')
    ->name('testimonials.approve');

/**
 * Admin: Reject testimonial
 * POST /testimonials/{testimonial}/reject
 * Admin route - mark testimonial as rejected (hidden)
 * 
 * Sets is_approved = false
 */
Route::post('/testimonials/{testimonial}/reject', [TestimonialController::class, 'reject'])
    ->middleware('auth')
    ->name('testimonials.reject');

/**
 * Admin: Mark testimonial as featured
 * POST /testimonials/{testimonial}/feature
 * Admin route - highlight special/excellent reviews
 * 
 * Featured testimonials appear first on service pages
 * Sets is_featured = true
 */
Route::post('/testimonials/{testimonial}/feature', [TestimonialController::class, 'feature'])
    ->middleware('auth')
    ->name('testimonials.feature');

/**
 * Admin: Unmark testimonial as featured
 * POST /testimonials/{testimonial}/unfeature
 * Admin route - remove featured status
 * 
 * Sets is_featured = false
 */
Route::post('/testimonials/{testimonial}/unfeature', [TestimonialController::class, 'unfeature'])
    ->middleware('auth')
    ->name('testimonials.unfeature');

/**
 * Admin: Delete testimonial
 * DELETE /testimonials/{testimonial}
 * Admin route - permanently remove testimonial
 */
Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])
    ->middleware('auth')
    ->name('testimonials.destroy');

/**
 * Get testimonials for service API endpoint
 * GET /api/testimonials/service/{service_id}
 * Public API endpoint - get approved testimonials for a service
 * 
 * Parameters:
 * - service_id: ID of service
 * - page: Page number (default 1)
 * - per_page: Results per page (default 5)
 * - sort: 'recent' or 'rating' (default 'recent')
 * 
 * Returns JSON with paginated testimonials
 */
Route::get('/api/testimonials/service', [TestimonialController::class, 'getForService'])
    ->name('testimonials.api.service');

/**
 * Get testimonials for profile API endpoint
 * GET /api/testimonials/profile/{profile_id}
 * Public API endpoint - get approved testimonials for a profile
 * 
 * Parameters:
 * - profile_id: ID of profile
 * - page: Page number (default 1)
 * - per_page: Results per page (default 5)
 * - sort: 'recent' or 'rating' (default 'recent')
 * 
 * Returns JSON with paginated testimonials
 */
Route::get('/api/testimonials/profile', [TestimonialController::class, 'getForProfile'])
    ->name('testimonials.api.profile');

/**
 * ========== PAYMENT ROUTES (PHASE 5) ==========
 * Stripe payment integration and management
 */

// Create payment
Route::get('/payments/create', [\App\Http\Controllers\PaymentController::class, 'create'])
    ->middleware('auth')
    ->name('payments.create');

// Store payment
Route::post('/payments', [\App\Http\Controllers\PaymentController::class, 'store'])
    ->middleware('auth')
    ->name('payments.store');

// Show payment details
Route::get('/payments/{payment}', [\App\Http\Controllers\PaymentController::class, 'show'])
    ->middleware('auth')
    ->name('payments.show');
Route::post('/payments/{payment}/proof', [\App\Http\Controllers\PaymentController::class, 'submitProof'])
    ->middleware('auth')->name('payments.proof.store');
Route::get('/payments/{payment}/proof', [\App\Http\Controllers\PaymentController::class, 'proof'])
    ->middleware('auth')->name('payments.proof.show');
Route::post('/payments/{payment}/review', [\App\Http\Controllers\PaymentController::class, 'review'])
    ->middleware('auth')->name('payments.review');

// Confirm payment
Route::post('/payments/{payment}/confirm', [\App\Http\Controllers\PaymentController::class, 'confirm'])
    ->middleware('auth')
    ->name('payments.confirm');

// List payments
Route::get('/payments', [\App\Http\Controllers\PaymentController::class, 'index'])
    ->middleware('auth')
    ->name('payments.index');

// Refund payment
Route::post('/payments/{payment}/refund', [\App\Http\Controllers\PaymentController::class, 'refund'])
    ->middleware('auth')
    ->name('payments.refund');

/**
 * ========== WEBHOOK ROUTES ==========
 * These should NOT require authentication
 * Stripe/WhatsApp will POST to these endpoints
 */

// Stripe webhook
Route::post('/webhooks/stripe', [\App\Http\Controllers\PaymentController::class, 'handleStripeWebhook'])
    ->name('webhooks.stripe');

// WhatsApp webhook (Phase 5)
Route::get('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'verifyWebhook'])
    ->name('webhooks.whatsapp.verify');
Route::post('/webhooks/whatsapp', [\App\Http\Controllers\WhatsAppController::class, 'handleWebhook'])
    ->name('webhooks.whatsapp');

/**
 * ========== CONTACT INQUIRY ROUTES ==========
 * Public form for visitors to submit inquiries
 */

// Show contact form
Route::get('/contact', [\App\Http\Controllers\ContactInquiryController::class, 'create'])
    ->name('contact.create');

// Submit inquiry
Route::post('/contact', [\App\Http\Controllers\ContactInquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

/**
 * ========== ADMIN ROUTES ==========
 * Future: Will add middleware('admin') to restrict these
 * For Phase 2, just creating the structure
 */

// Admin routes will be added in Phase 3+

/**
 * ========== ROUTE GROUPS (Useful for organizing) ==========

 * 
 * Example (not implemented yet):
 * 
 * Route::middleware('auth')->group(function () {
 *     Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
 *     Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
 *     Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
 * });
 * 
 * Route::middleware(['auth', 'admin'])->group(function () {
 *     Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
 *     Route::resource('/admin/users', AdminUserController::class);
 *     Route::resource('/admin/divisions', AdminDivisionController::class);
 * });
 */
