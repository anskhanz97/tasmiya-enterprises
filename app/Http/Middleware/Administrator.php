<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Administrator Middleware
 * 
 * Purpose: Restrict routes to admin users only
 * 
 * Usage in routes:
 *   Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
 *       ->middleware('admin');
 * 
 * What it does:
 * 1. Checks if user is authenticated
 * 2. Checks if user has admin role
 * 3. Allows access if both true
 * 4. Denies access (403) if not admin
 * 
 * Middleware flow:
 *
 * Request comes in
 *     ↓
 * [Authenticate Middleware] Is user logged in?
 *     ↓ Yes
 * [Administrator Middleware] Is user admin?
 *     ↓ Yes
 * [Route Handler] Execute the controller method
 *     ↓
 * Response sent to user
 *
 * If at any point the check fails:
 *     ↓
 * Access denied (403 Forbidden)
 * 
 * Security benefit:
 * - Cannot access admin routes even if you're logged in
 * - Only admin/super_admin roles can access
 * - Automatic, don't need to check in every controller
 * - Easy to add to any route
 * 
 * Why separate middleware:
 * - 'auth' middleware checks if logged in (general purpose)
 * - 'admin' middleware checks if admin (specific to admin routes)
 * - Can stack: middleware(['auth', 'admin'])
 * - Each middleware has single responsibility
 * 
 * What is auth()->user()?
 * - After 'auth' middleware, auth()->user() returns User model
 * - Contains all user data: id, name, email, role, division_id, etc
 * - Can call methods: auth()->user()->isAdmin()
 * 
 * What is $user->isAdmin()?
 * - Method in User model that returns boolean
 * - Checks if role is 'admin' or 'super_admin'
 * - Returns true/false
 * 
 * What does abort(403) do?
 * - Throws HTTP 403 (Forbidden) exception
 * - Laravel catches it and shows error page
 * - User sees: "403 Forbidden" or custom error page
 * - Logs the access denial attempt (security audit)
 * 
 * Alternative approaches:
 * 1. Check in controller:
 *    if (!auth()->user()->isAdmin()) {
 *        abort(403);
 *    }
 *    (Less DRY, repeats in multiple places)
 * 
 * 2. Use Laravel's Gate:
 *    Route::get(...)->middleware('can:is-admin')
 *    (More complex, not needed for simple role check)
 * 
 * 3. Use Policy class:
 *    $this->authorize('admin')
 *    (Better for model-based permissions like "edit profile")
 * 
 * For admin access, middleware is perfect (simple and clear)
 */
class Administrator
{
    /**
     * Handle an incoming request.
     * 
     * Parameters:
     * - $request: The HTTP request object
     *   Contains: URL, method, form data, headers, user info, etc
     * - $next: The next middleware/handler to call
     *   Callable that represents the rest of the request pipeline
     * 
     * Returns: Response object
     *   The HTTP response to send to user
     * 
     * How middleware works:
     * 1. Middleware runs BEFORE the route handler
     * 2. Can inspect request
     * 3. Can block request (return error response)
     * 4. Or allow request to continue by calling $next()
     * 5. The handler (controller) runs inside $next()
     * 6. The response comes back through middleware
     * 7. Middleware can modify response before sending
     * 
     * Example flow:
     *
     * Request: GET /admin/users
     *     ↓
     * [Middleware 1] Check HTTP method
     *     ↓ Allowed
     * [Middleware 2] Check authentication
     *     ↓ User logged in
     * [Middleware 3] Check admin role (THIS MIDDLEWARE)
     *     ↓ User is admin? 
     *       Yes → Continue ✓
     *       No → Abort 403 ✗
     *     ↓ If Yes, continue
     * [Route Handler] Execute controller
     *     ↓
     * Response (HTML/JSON)
     *     ↓
     * Back through middlewares
     *     ↓
     * Return to user
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Get authenticated user
         * 
         * auth()->user() returns:
         * - User model instance if logged in
         * - null if not logged in
         * 
         * Note: At this point, user MUST be logged in
         * (The 'auth' middleware already checked this)
         * So auth()->user() will never be null
         * 
         * But we check anyway for safety
         * Defensive programming: Don't assume previous middleware ran
         */
        $user = $request->user();

        // Check if user is authenticated and is admin
        // If not, deny access
        if (!$user || !$user->isAdmin()) {
            /**
             * User is not authenticated or not admin
             * Deny access with 403 Forbidden status code
             * 
             * abort(403) does:
             * 1. Throws HttpException(403)
             * 2. Laravel catches it
             * 3. Renders error view (resources/views/errors/403.blade.php)
             * 4. Returns 403 status code
             * 5. Logs the incident
             * 
             * Optional: Can pass message
             * abort(403, 'You are not authorized to access this page.')
             * Message shown in error view
             */
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        // User is authenticated and is admin
        // Allow request to continue to next middleware/handler
        return $next($request);
    }
}
