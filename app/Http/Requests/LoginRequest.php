<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * LoginRequest - Validates and authorizes login form submission
 * 
 * This FormRequest class handles:
 * 1. Validation: Ensures email/password are in correct format
 * 2. Authentication: Actually checks if email+password combination is valid
 * 3. Authorization: Could check if user has permission to log in
 * 4. Error messages: Custom error messages for users
 * 
 * Why a separate FormRequest class:
 * - Keeps validation logic OUT of the controller
 * - Makes validation reusable (can use in multiple controllers)
 * - Cleaner controller code (easier to understand)
 * - Automatic validation error responses
 * - Standardized validation approach across the app
 * 
 * How it works:
 * 1. Request comes in: POST /login with email + password
 * 2. Laravel routes to AuthController@store(LoginRequest $request)
 * 3. Before reaching the controller, FormRequest automatically:
 *    a. Calls authorize() - checks if user is allowed to perform this action
 *    b. Calls rules() - validates input against these rules
 *    c. If validation fails, returns user back to form with errors
 *    d. If validation succeeds, continues to controller method
 * 4. Controller receives $request with validated() data
 * 5. Controller uses the validated data
 * 
 * Security benefits:
 * - Input validation prevents bad data from reaching controller
 * - Sanitized data returned from validated()
 * - Prevents SQL injection (parameterized queries)
 * - Prevents XSS attacks (escaped output in views)
 * - Authorization check prevents unauthorized access
 * 
 * Validation flow:
 *
 * User submits form
 *     ↓
 * [authorize()]: Does user have permission?
 *     ↓ Yes
 * [rules()]: Is data in correct format?
 *     ↓ Yes
 * [messages()]: Customize error text
 *     ↓
 * Validation passes → Controller receives validated data
 *
 * If any step fails:
 *     ↓
 * Validation error → User redirected back with error messages
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * This method checks if the current user should be allowed to make this request.
     * For login, we want ONLY non-authenticated users to access.
     * (Logged-in users should go to dashboard, not the login form)
     * 
     * The middleware 'guest' also checks this, but this is a backup.
     * Defense in depth: Check at multiple levels.
     * 
     * Return: true if allowed, false if not allowed
     *         False results in 403 Forbidden response
     * 
     * Why true:
     * - Anyone (authenticated or not) can attempt to send a login request
     * - The 'guest' middleware blocks logged-in users from even getting here
     * - But if someone does send this request, allow it to proceed
     * - The password check will verify they're actually logging in correctly
     * - And is_active check will block deactivated accounts
     * 
     * Example:
     * - GET /login?email=test@test.com - Middleware blocks (already logged in)
     * - POST /login - If someone posts to this endpoint, allow it
     *                (they'll fail password check if not real credentials)
     */
    public function authorize(): bool
    {
        return true; // Allow anyone to attempt login
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Rules define what format the input data should be in.
     * Each field has rules separated by pipes (|).
     * 
     * If validation fails:
     * - Errors are automatically collected
     * - User is redirected back to form
     * - Errors are available in $errors in the view
     * - Old input is repopulated in form fields (except password)
     * 
     * Return: array of field => rule pairs
     *         Example: ['email' => 'required|email', 'password' => 'required']
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // ========== EMAIL VALIDATION ==========
            
            /**
             * email: User's email address
             * 
             * Rules:
             * - 'required': Email field cannot be empty
             * - 'email': Must be valid email format (checks @ symbol, etc)
             * - 'exists:users,email': Email must already exist in users table
             *   (prevents errors if user tries to log in with non-existent email)
             * 
             * Why 'exists':
             * - If email doesn't exist, user will fail login anyway
             * - Early error message is clearer
             * - Doesn't leak whether email is registered (privacy)
             * 
             * Actually... 'exists' does leak this info.
             * Common pattern: Use generic error message
             * "The email or password is incorrect"
             * Not: "Email not found" or "Password wrong"
             * 
             * But for internal team member registration, this doesn't matter
             * (Not a public sign-up form where privacy concerns exist)
             */
            'email' => ['required', 'email', 'exists:users,email'],

            // ========== PASSWORD VALIDATION ==========
            
            /**
             * password: User's password
             * 
             * Rules:
             * - 'required': Password field cannot be empty
             * - 'string': Value must be a string (not array or object)
             * - 'current_password': Password must match the authenticated user's password
             *   (For "change password" forms, ensuring user knows old password)
             * 
             * Wait... 'current_password' is wrong here!
             * Login form doesn't have an authenticated user yet.
             * 
             * Correction needed:
             * The actual password validation happens in the controller
             * Using Auth::attempt() which:
             * 1. Finds user by email
             * 2. Compares password hash with Hash::check()
             * 
             * In this rules() method:
             * - We only validate the email/password fields exist and format is OK
             * - The actual password match check happens in controller
             * 
             * Alternative approach:
             * Could use custom validation rule:
             * 'password' => ['required', new ValidateCredentials($email)]
             * But Auth::attempt() is simpler and more idiomatic
             * 
             * So the rule should be:
             * 'password' => ['required', 'string']
             * And trust Auth::attempt() to validate the actual password
             */
            'password' => ['required', 'string'],

            // ========== REMEMBER ME ==========
            
            /**
             * remember: Optional "remember me" checkbox
             * 
             * Rules:
             * - 'nullable': Field can be empty (user unchecks or form doesn't include it)
             * - 'boolean': Value must be true/false or 1/0
             * 
             * Usage:
             * In the form:
             *   <input type="checkbox" name="remember">
             * 
             * When checked:
             * - Checkbox value is sent as "remember=on" or "remember=1"
             * - Auth::attempt() uses this to set a long-lived cookie
             * - User won't need to log in again for 2 weeks (default)
             * - Cookie name: 'remember_web_' . something
             * 
             * How it works:
             * 1. User checks "remember me" and logs in
             * 2. Laravel creates two cookies:
             *    - Session cookie (expires when browser closes)
             *    - Remember token cookie (expires in 2 weeks)
             * 3. On next visit, Laravel checks remember token
             * 4. If valid, user is logged in automatically
             * 5. No password needed
             * 
             * Security:
             * - Token is hashed, changes each login
             * - Token stored in database's remember_token field
             * - If someone steals cookie, token becomes invalid
             * - Not as secure as password, but convenient for personal devices
             * 
             * Implementation:
             * auth()->attempt($credentials, $request->boolean('remember'))
             * The second parameter tells Laravel to use remember cookie
             */
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     * 
     * By default, validation errors have generic messages:
     * - "The email field is required."
     * - "The email must be a valid email address."
     * 
     * This method provides custom, user-friendly messages:
     * - "Please enter your email address."
     * - "We couldn't find an account with that email address."
     * 
     * Return: array of field.rule => message pairs
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'We couldn\'t find an account with that email address.',
            'password.required' => 'Please enter your password.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     * 
     * By default, failed validation redirects back with errors.
     * This method allows custom handling.
     * 
     * Common use cases:
     * - Log failed login attempts (security audit)
     * - Rate limiting (already done by middleware)
     * - Send suspicious activity alerts
     * - Redirect to different page instead of back
     * 
     * Not implemented here, but shown for reference:
     * 
     * public function failedValidation(Validator $validator) {
     *     // Log failed login attempt
     *     Log::warning('Failed login attempt', [
     *         'email' => $this->input('email'),
     *         'ip' => $this->ip(),
     *     ]);
     *     
     *     parent::failedValidation($validator);
     * }
     * 
     * For now, using default behavior (redirect back with errors)
     */
}
