<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreServiceRequest
 *
 * Validates input when creating a new service.
 *
 * Validation Rules:
 * - name: Required, unique, max 100 chars
 * - description: Required, max 500 chars (short version)
 * - long_description: Optional, for detailed info
 * - base_price: Required, numeric, min 0
 * - division_id: Required, must exist in divisions table
 * - profiles: Optional array of profile IDs for service assignment
 *
 * Authorization:
 * This request doesn't perform authorization.
 * Authorization should be checked in controller via Gate::authorize()
 *
 * Message Customization:
 * Custom error messages provide user-friendly feedback.
 */
class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Authorization for creating services should be checked in the controller.
     * This request focuses on validation.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization check happens in ServiceController@store
        // via $this->authorize('create', Service::class)
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
            // Service Name
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:services,name', // Prevent duplicate service names
            ],

            // Short Description (for listings)
            'description' => [
                'required',
                'string',
                'max:500',
            ],

            // Long Description (optional, for detail pages)
            'long_description' => [
                'nullable',
                'string',
            ],

            // Icon/Image URL
            'icon_url' => [
                'nullable',
                'url',
            ],

            'image_url' => [
                'nullable',
                'url',
            ],

            // Pricing Information
            'base_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],

            'currency' => [
                'nullable',
                'in:PKR,USD,EUR,GBP',
            ],

            // Division Assignment
            'division_id' => [
                'required',
                'exists:divisions,id', // Must be valid division
            ],

            // Profile Assignment (many-to-many)
            'profiles' => [
                'nullable',
                'array',
            ],

            'profiles.*' => [
                'exists:profiles,id', // Each profile must exist
            ],
        ];
    }

    /**
     * Custom validation error messages.
     *
     * Provides user-friendly feedback instead of technical jargon.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Service name is required.',
            'name.unique' => 'A service with this name already exists.',
            'name.max' => 'Service name cannot exceed 100 characters.',

            'description.required' => 'Service description is required.',
            'description.max' => 'Description cannot exceed 500 characters.',

            'base_price.required' => 'Base price is required.',
            'base_price.numeric' => 'Price must be a valid number.',
            'base_price.min' => 'Price cannot be negative.',

            'division_id.required' => 'Please select a division.',
            'division_id.exists' => 'Selected division is invalid.',

            'profiles.*.exists' => 'Selected profile does not exist.',

            'icon_url.url' => 'Icon URL must be a valid URL.',
            'image_url.url' => 'Image URL must be a valid URL.',

            'currency.in' => 'Currency must be PKR, USD, EUR, or GBP.',
        ];
    }

    /**
     * Prepare data for validation.
     *
     * This method is called before validation.
     * Use it to clean, transform, or normalize input data.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from text fields
        $this->merge([
            'name' => trim($this->input('name')),
            'description' => trim($this->input('description')),
            'long_description' => trim($this->input('long_description')),
        ]);

        // Set default currency if not provided
        if (! $this->has('currency') || empty($this->input('currency'))) {
            $this->merge([
                'currency' => 'PKR'
            ]);
        }
    }
}
