<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * ProfileUpdateRequest - Validate and authorize profile updates
 * 
 * Form Request Pattern:
 * 
 * The FormRequest class is Laravel's way of handling validation and authorization
 * in one place instead of spreading them throughout the controller.
 * 
 * Lifecycle:
 * 1. Controller method receives this class type: update(ProfileUpdateRequest $request)
 * 2. Laravel intercepts and creates an instance of this class
 * 3. Calls authorize() method - check permission
 * 4. Calls rules() method - validate input
 * 5. If validation fails: returns to form with errors
 * 6. If valid: injects into controller method
 * 7. Controller calls $request->validated() to get safe data
 * 
 * Benefits:
 * - Validation logic separate from controller (DRY principle)
 * - Can be reused across multiple controllers if needed
 * - Cleaner controller code
 * - Easier to test validation independently
 * - Authorization check in same place as validation
 * 
 * This specific request handles:
 * - Validating profile update input
 * - Ensuring email uniqueness (except own email)
 * - Validating text lengths
 * - Sanitizing array inputs (specializations, qualifications, etc.)
 * - Authorization: Can user edit this profile?
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Authorize the request
     * 
     * Called before validation. If returns false, throws 403 Forbidden
     * 
     * Authorization rules:
     * - User can edit their own profile: $user->id === $profile->user_id
     * - Admin can edit any profile: $user->isAdmin()
     * 
     * How to get the profile being edited:
     * From route: $this->route('profile')
     * The route parameter {profile} is available via $this->route('profile')
     * 
     * Example routes for reference:
     * PUT /profile/{profile} - {profile} parameter is available here
     * 
     * @return bool - True if authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Get the profile being edited from route parameter
        $profile = $this->route('profile');

        // Get authenticated user - use injected user() method from FormRequest
        $user = $this->user();

        // User can edit their own profile
        if ($user && $user->id === $profile->user_id) {
            return true;
        }

        // Admin can edit any profile
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Everyone else is denied
        return false;
    }

    /**
     * Validation rules for profile updates
     * 
     * Rules format: 'field' => ['rule1', 'rule2', ...]
     * 
     * Common rules:
     * - required: Field must have value
     * - nullable: Field can be empty
     * - string: Must be text
     * - email: Must be valid email format
     * - unique: Value must be unique in database (with exceptions)
     * - array: Must be an array
     * - min/max: Length constraints
     * - regex: Must match regex pattern
     * 
     * Array rule syntax:
     * Rule::unique('table', 'column')
     *   ->where('condition', value)
     *   ->ignore($id)
     * 
     * This creates: WHERE column != excluded_value AND condition = value
     * 
     * @return array - Validation rules
     */
    public function rules(): array
    {
        // Get the profile being updated for email uniqueness check
        // We need to exclude the current email from the unique check
        $profile = $this->route('profile');

        return [
            // Bio - user's biography/about section
            'bio' => [
                'nullable',                  // Can be empty
                'string',                    // Must be text
                'max:500',                   // Max 500 characters
            ],

            // Specializations - array of skills
            'specializations' => [
                'nullable',                  // Can be empty
                'array',                     // Must be array
                'max:10',                    // Max 10 items
            ],
            'specializations.*' => [
                'nullable',
                'string',                    // Each item must be string
                'max:50',                    // Max 50 chars per specialization
                'min:2',                     // Min 2 chars per specialization
            ],

            // Experience years - integer
            'experience_years' => [
                'nullable',                  // Can be empty
                'integer',                   // Must be whole number
                'min:0',                     // Can't be negative
                'max:80',                    // Reasonable upper limit
            ],

            // Profile image URL
            'profile_image_url' => [
                'nullable',                  // Can be empty (use placeholder)
                'string',                    // Must be text (URL)
                'url',                       // Must be valid URL format
                'max:255',                   // Max URL length
            ],
            
            // Profile image file upload
            'profile_image' => [
                'nullable',                  // Can be empty
                'image',                     // Must be image file
                'mimes:jpeg,jpg,png,gif,webp',  // Allowed formats
                'max:5120',                  // Max 5MB (in kilobytes)
            ],

            // Banner image URL
            'banner_image_url' => [
                'nullable',
                'string',
                'url',
                'max:255',
            ],
            
            // Banner image file upload
            'banner_image' => [
                'nullable',                  // Can be empty
                'image',                     // Must be image file
                'mimes:jpeg,jpg,png,gif,webp',  // Allowed formats
                'max:5120',                  // Max 5MB (in kilobytes)
            ],

            'remove_profile_image' => ['nullable', 'boolean'],
            'remove_banner_image' => ['nullable', 'boolean'],
            'section_order' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $keys = explode(',', $value);
                    $expected = array_keys(\App\Models\Profile::SECTION_TITLES);
                    if (count($keys) !== count($expected) || array_diff($keys, $expected) || count(array_unique($keys)) !== count($expected)) {
                        $fail('The section order is invalid. Please reload the editor and try again.');
                    }
                },
            ],
            'section_titles' => ['required', 'array'],
            'section_titles.*' => ['required', 'string', 'max:60'],
            'section_descriptions' => ['nullable', 'array'],
            'section_descriptions.*' => ['nullable', 'string', 'max:160'],
            'section_visibility' => ['required', 'array'],
            'section_visibility.*' => ['required', 'boolean'],

            // Social links - JSON object with contact info
            'social_links' => [
                'nullable',                  // Can be empty
                'array',                     // Must be array
            ],
            'social_links.whatsapp' => [
                'nullable',                  // Can omit WhatsApp
                'string',                    // Phone number as string
                'regex:/^[+]?[0-9\s\-()]{7,}$/',  // Basic phone format
            ],
            'social_links.linkedin' => [
                'nullable',
                'string',
                'url',                       // Must be valid LinkedIn URL
            ],
            'social_links.twitter' => [
                'nullable',
                'string',
                'url',
            ],

            // Qualifications - array of education/certifications
            'qualifications' => [
                'nullable',
                'array',
                'max:10',
            ],
            'qualifications.*' => [
                'string',
                'max:100',
                'min:2',
            ],

            // Languages spoken
            'languages' => [
                'nullable',
                'array',
                'max:10',
            ],
            'languages.*' => [
                'string',
                'max:50',
                'min:2',
            ],

            // Consultation fee - hourly rate
            'consultation_fee' => [
                'nullable',                  // Not required
                'numeric',                   // Can be decimal
                'min:0',                     // Can't be negative
                'max:99999.99',              // Reasonable upper limit
            ],

            // Visibility toggle
            'is_visible' => [
                'nullable',                  // Can be empty (defaults to current)
                'boolean',                   // Must be true/false or 0/1
            ],
        ];
    }

    /**
     * Custom error messages for validation failures
     * 
     * Format: 'field.rule' => 'User-friendly error message'
     * 
     * If not specified, Laravel uses default messages
     * These custom messages are more helpful to users
     * 
     * @return array - Custom error messages
     */
    public function messages(): array
    {
        return [
            'bio.max' => 'Bio must not exceed 500 characters.',
            'experience_years.min' => 'Experience must be 0 or greater.',
            'experience_years.max' => 'Please enter a valid experience level.',
            'experience_years.integer' => 'Experience must be a whole number.',
            'profile_image_url.url' => 'Please provide a valid image URL.',
            'banner_image_url.url' => 'Please provide a valid banner URL.',
            'profile_image.image' => 'Profile picture must be an image file.',
            'profile_image.mimes' => 'Profile picture must be JPEG, PNG, GIF, or WebP format.',
            'profile_image.max' => 'Profile picture must not exceed 5MB.',
            'banner_image.image' => 'Banner image must be an image file.',
            'banner_image.mimes' => 'Banner image must be JPEG, PNG, GIF, or WebP format.',
            'banner_image.max' => 'Banner image must not exceed 5MB.',
            'consultation_fee.numeric' => 'Fee must be a valid number.',
            'consultation_fee.min' => 'Fee cannot be negative.',
            'social_links.whatsapp.regex' => 'Please enter a valid WhatsApp number.',
            'social_links.linkedin.url' => 'Please provide a valid LinkedIn profile URL.',
            'specializations.*.max' => 'Each specialization must be 50 characters or less.',
            'qualifications.*.max' => 'Each qualification must be 100 characters or less.',
            'languages.*.max' => 'Each language must be 50 characters or less.',
        ];
    }

    /**
     * Prepare data for validation
     * 
     * Called before validation
     * Allows modifying input before rules are applied
     * 
     * Useful for:
     * - Trimming whitespace
     * - Converting empty strings to null
     * - Normalizing data format
     * - Cleaning up input
     * 
     * Example:
     * If user submits empty string for bio, convert to null
     * This satisfies 'nullable' rule better than empty string
     */
    protected function prepareForValidation(): void
    {
        // Convert empty arrays to null
        if ($this->has('specializations') && empty($this->specializations)) {
            $this->merge(['specializations' => null]);
        }

        if ($this->has('qualifications') && empty($this->qualifications)) {
            $this->merge(['qualifications' => null]);
        }

        if ($this->has('languages') && empty($this->languages)) {
            $this->merge(['languages' => null]);
        }

        // Trim whitespace from bio
        if ($this->has('bio')) {
            $this->merge(['bio' => trim($this->bio ?? '')]);
        }

        // Clean phone number - remove spaces and special chars
        if ($this->has('social_links.whatsapp')) {
            $phone = $this->input('social_links.whatsapp');
            if ($phone) {
                // Keep only digits and + sign
                $phone = preg_replace('/[^\d+]/', '', $phone);
                $this->merge(['social_links' => array_merge(
                    $this->social_links ?? [],
                    ['whatsapp' => $phone]
                )]);
            }
        }
    }
}
