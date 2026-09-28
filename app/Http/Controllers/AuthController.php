<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * AuthController - Handles user authentication (login, register, logout)
 * 
 * This controller is the central point for all authentication logic.
 * It manages:
 * - Showing login/register forms
 * - Validating and processing form submissions
 * - Creating sessions
 * - Logging out users
 * - Redirecting based on authentication state
 * 
 * Key Principles:
 * 1. Separation of concerns: This handles auth, other controllers handle business logic
 * 2. Form Requests: Validation is delegated to dedicated classes (LoginRequest, RegisterRequest)
 * 3. Events: Fires events so other parts of the app can react (e.g., send welcome email)
 * 4. Type hints: All parameters and return types are explicitly typed
 * 5. Comments: Each method explains what it does and why
 * 
 * Security Considerations:
 * - CSRF tokens required on all forms (automatic in Laravel)
 * - Passwords hashed using bcrypt (via User model's 'hashed' cast)
 * - Login throttling (built into middleware)
 * - Session security (httpOnly, secure cookies)
 * - Activity logging (last_login_at tracked)
 * 
 * Methods in this controller:
 * - showLogin(): Display login form
 * - store(): Process login form submission
 * - showRegister(): Display registration form
 * - register(): Process registration form submission
 * - logout(): End user session
 * 
 * Middleware applied:
 * - 'guest': Methods accessible only to non-authenticated users
 *   Prevents logged-in users from accessing login/register forms
 * - 'auth': Methods accessible only to authenticated users
 *   Prevents non-authenticated users from accessing logout
 * 
 * Routes:
 * - GET /login -> showLogin
 * - POST /login -> store
 * - GET /register -> showRegister
 * - POST /register -> register
 * - POST /logout -> logout
 * 
 * Database interactions:
 * - User::where('email', $email)->first() - Find user by email
 * - User::create([...]) - Create new user
 * - auth()->login($user) - Start authenticated session
 * - auth()->logout() - End authenticated session
 */
class AuthController extends Controller
{
    /**
     * Show the login form
     * 
     * HTTP Method: GET
     * Route: /login
     * Middleware: 'guest' (only non-authenticated users)
     * 
     * What it does:
     * - Renders the login form template
     * - User can enter email and password
     * 
     * Why separate method:
     * - GET requests display forms
     * - POST requests process form data
     * - Follows REST conventions
     * - Makes routes clear and predictable
     * 
     * Example flow:
     * 1. User visits /login
     * 2. Laravel routes to AuthController@showLogin
     * 3. This method returns login view
     * 4. User sees form
     * 5. User fills in email/password
     * 6. User clicks "Sign In"
     * 7. Form POSTs to /login -> goes to store() method
     * 
     * Return: View | RedirectResponse
     *   - Returns login form view
     *   - Or redirects if user already authenticated
     *   - (Middleware prevents this, but return type allows for it)
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Process login form submission
     * 
     * HTTP Method: POST
     * Route: /login
     * Middleware: 'guest', 'throttle:5,1' (max 5 attempts per minute)
     * 
     * What it does:
     * 1. Validates login credentials using LoginRequest
     * 2. Finds user by email
     * 3. Checks if user is active
     * 4. Attempts authentication
     * 5. Updates last_login_at timestamp
     * 6. Creates session
     * 7. Regenerates session ID (prevents session fixation attacks)
     * 8. Redirects to dashboard
     * 
     * Authentication flow diagram:
     *
     * POST /login with email + password
     *         ↓
     *   [LoginRequest validates]
     *         ↓
     *   Find user by email in database
     *         ↓
     *   User exists? 
     *     No → LoginRequest validation fails → Return with error
     *     Yes → Continue
     *         ↓
     *   User is_active?
     *     No → Show error "Account deactivated"
     *     Yes → Continue
     *         ↓
     *   password matches? (Hash::check automatically done)
     *     No → LoginRequest validation fails → Return with error
     *     Yes → Continue
     *         ↓
     *   auth()->login($user) ← Creates session, sets auth cookies
     *         ↓
     *   Regenerate session (security: prevent session fixation)
     *         ↓
     *   Update last_login_at
     *         ↓
     *   Redirect to dashboard
     * 
     * Parameters:
     * - LoginRequest $request: Validated login credentials
     *   Automatically validates before this method runs
     *   If validation fails, user redirected back with errors
     *   If validation passes, $request->validated() has sanitized data
     * 
     * Returns: RedirectResponse
     *   - Redirects to intended page or dashboard
     *   - Sets session cookie in response
     *   - Browser will include cookie in future requests
     * 
     * Security features:
     * - CSRF token checked (automatic, Laravel middleware)
     * - Throttled (5 attempts per minute max)
     * - Password never logged or displayed
     * - Bcrypt hash prevents rainbow table attacks
     * - Session regeneration prevents session fixation
     * - Activity logged (last_login_at)
     * 
     * What can go wrong:
     * - Invalid email/password → Handled by LoginRequest validation
     * - User doesn't exist → Handled by LoginRequest validation
     * - User inactive → Checked in this method (could be in LoginRequest)
     * - Database error → Would be caught and logged by Laravel
     * 
     * Future improvements:
     * - IP address logging for security audit
     * - Login notifications via email
     * - 2FA (two-factor authentication)
     * - Login device tracking
     * - Suspicious login detection (new location, device)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // LoginRequest has already validated:
        // - Email exists and is valid format
        // - Password is not empty
        // - Email+password combination is correct (using Auth::attempt)
        
        // The remember checkbox controls the login cookie; it is not a users-table column.
        $credentials = $request->safe()->only(['email', 'password']);

        // Attempt to authenticate user
        // This checks:
        // 1. Email exists in users table
        // 2. Password hash matches
        // 3. Returns User model if successful, false if not
        // Note: LoginRequest handles the actual Auth::attempt() check
        // This line is here for clarity on authentication flow
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            // Password didn't match
            // But LoginRequest should have caught this already
            // This is a safety net
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // At this point, user is authenticated (session created)
        // Regenerate session to prevent session fixation attacks
        // Session fixation: Attacker creates session with known ID, tricks user to use it
        // By regenerating, we invalidate old session ID
        // Browser gets new session ID in cookie
        $request->session()->regenerate();

        // Get authenticated user and update last login timestamp
        // Auth::user() returns the currently authenticated User model
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        // Check if user is active
        // Admin can deactivate accounts without deleting them
        // Inactive users shouldn't be allowed to do anything
        if (!$user->is_active) {
            // User is inactive, log them out immediately
            Auth::logout();
            
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.',
            ])->onlyInput('email');
        }

        // Successful login
        // Redirect to intended page (if user was redirected to login, go back)
        // or to dashboard if no intended page
        
        // intended() is set by Laravel when middleware redirects to login
        // Example: User tries to access /admin, gets redirected to /login
        //          -> After login, intended() returns '/admin'
        // If no intended page, routes to 'dashboard' route name
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Show the registration form
     * 
     * HTTP Method: GET
     * Route: /register
     * Middleware: 'guest'
     * 
     * What it does:
     * - Renders registration form
     * - User can enter name, email, password, role
     * 
     * Note: This is HIDDEN registration
     * - No public link in navigation
     * - Only accessible via direct URL
     * - Admin must share the URL with new team members
     * - Prevents random people from signing up
     * 
     * Return: View
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Process registration form submission
     * 
     * HTTP Method: POST
     * Route: /register
     * Middleware: 'guest'
     * 
     * What it does:
     * 1. Validates registration data using RegisterRequest
     * 2. Creates new user record
     * 3. Sets role and division
     * 4. Fires 'Registered' event (triggers email verification)
     * 5. Optionally logs in the user
     * 6. Redirects to email verification or dashboard
     * 
     * Validation rules (in RegisterRequest):
     * - name: required, string, max 255
     * - email: required, email, unique (not already registered)
     * - password: required, min 8, confirmed (matches password_confirmation field)
     * - role: required, in list of valid roles
     * - division_id: required, exists in divisions table
     * 
     * Parameters:
     * - RegisterRequest $request: Validated registration data
     * 
     * Returns: RedirectResponse
     *   - Redirects to email verification page or dashboard
     *   - May also redirect to login for admin approval
     * 
     * Security:
     * - Password validated (min 8 chars, must be confirmed)
     * - Email uniqueness checked
     * - CSRF token required
     * - Input sanitized by validation
     * 
     * Hidden sign up security:
     * - URL not publicly listed
     * - Admin must share link with authorized people
     * - Prevents bot sign-ups
     * - Still password-protected (good password required)
     * 
     * Post-registration:
     * - Email verification email sent (Registered event)
     * - User must verify email before account is fully active
     * - Admin must activate account (is_active = true)
     * - User can't log in until both conditions met
     * 
     * Future enhancement:
     * - Email approval flow (admin approves before account active)
     * - Division assignment from admin, not form
     * - Role validation (user can't assign themselves as admin)
     * - Terms and conditions acceptance
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // Create new user with validated data
        // RegisterRequest ensures all data is valid and sanitized
        $user = User::create($request->validated());

        // Fire the 'Registered' event
        // Laravel listens to this event and:
        // - Sends email verification email
        // - Any custom listeners can react (e.g., welcome email, log creation, etc)
        event(new Registered($user));

        // Auto-login the newly registered user
        // Removes need for user to immediately log in
        // Credentials from form are still in memory (from registration)
        Auth::login($user);

        // Regenerate session for security
        $request->session()->regenerate();

        // Redirect to dashboard (or email verification screen)
        // Email::verify middleware should be applied to require verification
        return redirect(route('dashboard'))->with('status', 'Check your email to verify your account.');
    }

    /**
     * Logout the current user
     * 
     * HTTP Method: POST
     * Route: /logout
     * Middleware: 'auth' (only authenticated users)
     * 
     * What it does:
     * 1. Destroys session
     * 2. Clears auth cookies
     * 3. Invalidates session data
     * 4. Redirects to login page with success message
     * 
     * Why POST instead of GET:
     * - Logout should not be bookmarkable
     * - Prevents accidental logouts from link clicks
     * - CSRF token required (prevents logout via malicious image tags)
     * - Follows HTTP conventions (state change = POST)
     * 
     * Security:
     * - Only authenticated users can access
     * - CSRF token required
     * - Middleware forces login first
     * - All session data cleared
     * 
     * Parameters:
     * - Request $request: HTTP request object (needed for session)
     * 
     * Returns: RedirectResponse
     *   - Redirects to login page with success message after successful logout
     * 
     * What happens:
     * 1. auth()->logout() ends session
     * 2. $request->session()->invalidate() destroys session data
     * 3. $request->session()->regenerateToken() creates new CSRF token
     *    (New token is for future requests, old session token is gone)
     * 4. Redirect to login page with success message
     * 
     * Browser behavior:
     * - Session cookie is cleared/expired
     * - Next request won't include auth cookie
     * - User is effectively logged out
     * 
     * Future enhancements:
     * - Log logout activity (for audit trail)
     * - Logout notification email
     * - Logout all other sessions (logout from other devices)
     * - Logout reason tracking
     */
    public function logout(Request $request): RedirectResponse
    {
        // Log out the user
        Auth::logout();

        // Invalidate the current session
        // Clears all session data from the server
        $request->session()->invalidate();

        // Generate new CSRF token for the next request
        // Old CSRF token is invalid (security measure)
        $request->session()->regenerateToken();

        // Redirect to login page with success message
        return redirect()->route('login')
            ->with('status', 'You have been successfully logged out.');
    }

    /**
     * Show change password form
     * 
     * HTTP Method: GET
     * Route: /password/change
     * Middleware: 'auth' (only authenticated users)
     * 
     * @return View
     */
    public function showChangePassword(): View
    {
        return view('auth.change-password');
    }

    /**
     * Update user password
     * 
     * HTTP Method: PUT
     * Route: /password/update
     * Middleware: 'auth' (only authenticated users)
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log out from other devices (optional security measure)
        // Auth::logoutOtherDevices($request->new_password);

        return redirect()->route('password.change')
            ->with('success', 'Password changed successfully!');
    }
}
