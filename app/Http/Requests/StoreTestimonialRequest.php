<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Service;
use App\Models\Profile;

/**
 * StoreTestimonialRequest
 *
 * Validates input when submitting a new testimonial.
 *
 * Validation Rules:
 * - content: 20-500 characters (substantive review required)
 * - rating: 1-5 stars (integer only)
 * - author_name: Required, max 100 chars
 * - author_position: Optional job title
 * - author_company: Optional company name
 * - author_image_url: Optional, must be valid URL
 * - Must link to either service OR profile (or both)
 *
 * Security Features:
 * - Content length prevents spam/short reviews
 * - Rating validation prevents invalid values
 * - Author information validation prevents malicious input
 * - Service/Profile existence validation
 */
class StoreTestimonialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Anyone can submit a testimonial (no auth required).
     * Testimonials are moderated by admin before appearing.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Public testimonials - no auth required
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Review Content
            'content' => [
                'required',
                'string',
                'min:20', // Prevent very short reviews
                'max:500',
            ],

            // Star Rating
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5', // 1-5 stars only
            ],

            // Author Information
            'author_name' => [
                'required',
                'string',
                'max:100',
            ],

            'author_position' => [
                'nullable',
                'string',
                'max:100',
            ],

            'author_company' => [
                'nullable',
                'string',
                'max:100',
            ],

            'author_image_url' => [
                'nullable',
                'url',
            ],

            // Testimonial Target (must be for service or profile or both)
            'service_id' => [
                'nullable',
                'exists:services,id',
            ],

            'profile_id' => [
                'nullable',
                'exists:profiles,id',
            ],

            // Redirect URL (where to return after submission)
            'redirect_to' => [
                'nullable',
                'url',
            ],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Please share your feedback.',
            'content.min' => 'Review must be at least 20 characters.',
            'content.max' => 'Review cannot exceed 500 characters.',

            'rating.required' => 'Please select a star rating.',
            'rating.integer' => 'Rating must be a whole number.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',

            'author_name.required' => 'Please provide your name.',
            'author_name.max' => 'Name cannot exceed 100 characters.',

            'author_position.max' => 'Position cannot exceed 100 characters.',
            'author_company.max' => 'Company name cannot exceed 100 characters.',

            'author_image_url.url' => 'Image URL must be a valid URL.',

            'service_id.exists' => 'Selected service is invalid.',
            'profile_id.exists' => 'Selected profile is invalid.',

            'redirect_to.url' => 'Invalid redirect URL.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * Provides user-friendly field names in error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'content' => 'review',
            'rating' => 'star rating',
            'author_name' => 'your name',
            'author_position' => 'position/title',
            'author_company' => 'company',
            'author_image_url' => 'profile image',
        ];
    }

    /**
     * Prepare data for validation and add custom validations.
     *
     * This method is called before validation runs.
     * We use it to:
     * 1. Trim whitespace from text fields
     * 2. Ensure at least service_id or profile_id is provided
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from text fields
        $this->merge([
            'content' => trim($this->input('content')),
            'author_name' => trim($this->input('author_name')),
            'author_position' => trim($this->input('author_position')),
            'author_company' => trim($this->input('author_company')),
        ]);
    }

    /**
     * Configure the validator instance with custom rules.
     *
     * This method allows adding validation logic after instantiation.
     * We use it to ensure testimonial is for at least one model (service or profile).
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $serviceId = $this->input('service_id');
            $profileId = $this->input('profile_id');

            // Must have at least service_id OR profile_id
            if (empty($serviceId) && empty($profileId)) {
                $validator->errors()->add(
                    'testimonial_target',
                    'Please review either a service or an expert.'
                );
            }
        });
    }

    /**
     * Get the validated data from the request.
     *
     * Override to handle type and model_id parameters.
     *
     * @param string|int|array|null $key
     * @param mixed $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        // Determine type based on which ID is provided
        if ($validated['service_id'] ?? null) {
            $validated['type'] = 'service';
            $validated['model_id'] = $validated['service_id'];
        } elseif ($validated['profile_id'] ?? null) {
            $validated['type'] = 'profile';
            $validated['model_id'] = $validated['profile_id'];
        }

        // Remove individual IDs from validated data (not used in create)
        unset($validated['service_id']);
        unset($validated['profile_id']);
        unset($validated['redirect_to']);

        return $validated;
    }
}
