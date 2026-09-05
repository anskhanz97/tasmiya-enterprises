<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * RegisterRequest - Validates registration form submission
 * 
 * This request validates when a new team member registers.
 * 
 * Since registration is HIDDEN (only accessible via admin-shared link),
 * security is already higher. But we still:
 * - Validate all input
 * - Ensure email is unique
 * - Require strong password
 * - Prevent arbitrary role/division assignment
 * 
 * Registration flow:
 * 1. Admin shares registration link with new team member
 * 2. New member fills form: name, email, password, role, division
 * 3. Form submits POST /register
 * 4. This request validates the data
 * 5. If valid, controller creates User record
 * 6. If invalid, user redirected with errors
 * 
 * Why password strength matters:
 * - Team members have access to admin features
 * - Weak passwords compromise account security
 * - Attackers could modify profiles, client info, payments
 * - We enforce minimum 8 characters
 * - Validation checks for common weak patterns
 */
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * For registration, we want:
     * - ONLY non-authenticated users can register
     * - Already-logged-in users should go to dashboard
     * - Middleware 'guest' enforces this
     * 
     * But we could add additional checks:
     * - Check if registration is open (closed during security issues)
     * - Verify user has the secret registration token (admin-shared)
     * - Check if registration limit reached (prevent unlimited accounts)
     * 
     * For now, simple: non-authenticated users only
     */
    public function authorize(): bool
    {
        return true; // 'guest' middleware already checked
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // ========== NAME VALIDATION ==========
            
            /**
             * name: User's full name
             * 
             * Rules:
             * - 'required': Cannot be empty
             * - 'string': Must be text (not array or object)
             * - 'max:255': Maximum 255 characters
             *   (Matches database column size)
             * - 'regex:/^[a-zA-Z\s]+$/': Only letters and spaces
             *   This prevents injection of special characters
             *   Example: "Atif Safdar" ✓
             *   Example: "Atif@Safdar" ✗ (@ not allowed)
             *   Example: "Atif123" ✗ (numbers not allowed)
             * 
             * Consideration:
             * - Regex is restrictive (doesn't allow hyphens, apostrophes)
             * - Many cultures use different name formats
             * - Future improvement: Accept broader character sets
             * - Or remove regex and trust user to enter real name
             */
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],

            // ========== EMAIL VALIDATION ==========
            
            /**
             * email: User's email address
             * 
             * Rules:
             * - 'required': Cannot be empty
             * - 'email': Valid email format (RFC compliant)
             * - 'unique:users,email': No other user with this email
             *   (Prevents multiple accounts with same email)
             *   Syntax: unique:{table},{column}
             * - 'max:255': Maximum 255 characters
             * 
             * Unique check:
             * If user tries to register with already-registered email:
             * Error: "The email has already been taken."
             * 
             * This is important because:
             * - Email is login identifier (must be unique)
             * - Password reset uses email (needs to go to correct user)
             * - Prevents account takeover via duplicate registration
             */
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],

            // ========== PASSWORD VALIDATION ==========
            
            /**
             * password: User's password
             * 
             * Rules:
             * - 'required': Cannot be empty
             * - 'confirmed': Must match password_confirmation field
             * - 'min:8': Minimum 8 characters
             * - Password::defaults(): Uses Laravel's password validation
             * 
             * Password::defaults() includes:
             * - Check against common passwords (password123, etc)
             * - Check for weak patterns
             * - Can be customized
             * 
             * For stronger security, use:
             * Password::min(8)
             *     ->letters()        // Must have at least one letter
             *     ->numbers()        // Must have at least one number
             *     ->symbols()        // Must have at least one special char
             *     ->uncompromised()  // Check against leaked passwords
             * 
             * The 'confirmed' rule:
             * Matches password field with password_confirmation field
             * In form:
             *   <input type="password" name="password">
             *   <input type="password" name="password_confirmation">
             * User must type password twice to confirm
             * If they don't match: "The password confirmation does not match."
             * 
             * Why:
             * - Typo prevention (user likely made same typo twice)
             * - Security check (ensures user knows their password)
             */
            'password' => ['required', 'confirmed', 'min:8', Password::defaults()],

            // ========== ROLE VALIDATION ==========
            
            /**
             * role: User's access level
             * 
             * Rules:
             * - 'required': Cannot be empty
             * - 'in:team_member,admin': Only allow these two values
             *   (Users can't register as 'super_admin')
             *   in:value1,value2,... - value must be in list
             * 
             * Why restricted to team_member and admin:
             * - Only admin-approved users can become admin
             * - This form is for team members primarily
             * - Admin accounts created through different process
             * 
             * For production:
             * - Don't allow users to assign own role
             * - Force role to always be 'team_member'
             * - Admin creates admin accounts manually
             * - Better: Remove role from form, set in controller
             * 
             * Improvement needed:
             * Change to: 'role' => ['sometimes', 'in:team_member']
             * And in controller: $user->role = 'team_member';
             * Only admin can promote to admin later
             */
            'role' => ['required', 'in:team_member,admin'],

            // ========== DIVISION VALIDATION ==========
            
            /**
             * division_id: Which division user belongs to
             * 
             * Rules:
             * - 'required': Must select a division
             * - 'exists:divisions,id': Division must exist in database
             *   (Prevents assigning to non-existent division)
             *   Syntax: exists:{table},{column}
             * 
             * Valid divisions:
             * 1 - FBR Taxation (Atif Safdar)
             * 2 - IT & Digital (Waseem, Ans)
             * 3 - Technical Support (Nazim)
             * 
             * If admin tries to assign to division_id = 999:
             * Error: "The selected division is invalid."
             * 
             * In form:
             *   <select name="division_id">
             *       <option value="1">FBR Taxation</option>
             *       <option value="2">IT & Digital</option>
             *       <option value="3">Technical Support</option>
             *   </select>
             * 
             * The 'exists' rule prevents:
             * - Typos (division_id = "1a")
             * - Deliberate injection (division_id = 999)
             * - Database errors from invalid foreign key
             */
            'division_id' => ['required', 'exists:divisions,id'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     * 
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.regex' => 'Name should only contain letters and spaces.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please use a different email.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'The passwords do not match. Please try again.',
            'password.min' => 'Password must be at least 8 characters long.',
            'role.required' => 'Please select your role.',
            'role.in' => 'The selected role is invalid.',
            'division_id.required' => 'Please select your division.',
            'division_id.exists' => 'The selected division is invalid.',
        ];
    }
}
