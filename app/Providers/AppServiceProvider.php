<?php

namespace App\Providers;

use App\Models\Profile;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Models\Service;
use App\Models\Testimonial;
use App\Policies\ProfilePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\UserPolicy;
use App\Policies\ServicePolicy;
use App\Policies\TestimonialPolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * Called during service registration phase
     * Use for binding classes to the container
     * Not commonly used in small apps
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * 
     * Called after all services are registered
     * Use for: model policies, view composers, event listeners, etc
     * 
     * This runs every request, so keep it lightweight
     */
    public function boot(): void
    {
        /**
         * Register model policies
         * 
         * Maps models to their authorization policies
         * Tells Laravel: "For User model authorization, use UserPolicy"
         * 
         * After this registration:
         * - $this->authorize('view', $user) uses UserPolicy@view()
         * - $this->authorize('update', $user) uses UserPolicy@update()
         * - @can('edit', $user) checks UserPolicy@update()
         * - etc
         * 
         * Without this mapping:
         * - Laravel looks for UserPolicy class automatically
         * - Convention over configuration (Laravel guesses UserPolicy from User model)
         * - But explicitly stating it is clearer
         * 
         * We're explicitly mapping for clarity and completeness
         * Shows that we're aware of the policy system
         * Makes code more discoverable
         */
        $this->registerPolicies();
    }

    /**
     * Register authorization policies
     * 
     * Helper method to keep boot() clean
     * All model policy mappings in one place
     */
    protected function registerPolicies(): void
    {
        // User model uses UserPolicy for all authorization checks
        // Model => Policy class mapping
        \Illuminate\Support\Facades\Gate::policy(User::class, UserPolicy::class);
        
        // Profile model uses ProfilePolicy for authorization checks
        // Enables: $this->authorize('view', $profile)
        // And: @can('edit', $profile) in Blade views
        \Illuminate\Support\Facades\Gate::policy(Profile::class, ProfilePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(Payment::class, PaymentPolicy::class);

        // Service model uses ServicePolicy for authorization checks
        // Controls who can create, edit, delete services
        // Admins only
        \Illuminate\Support\Facades\Gate::policy(Service::class, ServicePolicy::class);

        // Testimonial model uses TestimonialPolicy for authorization checks
        // Controls who can approve, feature, or delete testimonials
        // Admins only
        \Illuminate\Support\Facades\Gate::policy(Testimonial::class, TestimonialPolicy::class);

        // Project model uses ProjectPolicy for authorization checks
        // Controls who can create, edit, delete portfolio projects
        // Admins only
        \Illuminate\Support\Facades\Gate::policy(Project::class, ProjectPolicy::class);
    }
}

