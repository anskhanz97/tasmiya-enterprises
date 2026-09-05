<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateServiceRequest
 *
 * Validates input when updating an existing service.
 *
 * Similar to StoreServiceRequest but with adjustments for updates:
 * - name: Unique except for the current service
 * - division_id: Still required (cannot change division)
 *
 * The main difference from Store is the unique rule on name:
 * Store: unique:services,name
 * Update: unique:services,name,{id} (ignores current service)
 */
class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization check happens in ServiceController@update
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the service ID from the route
        $serviceId = $this->route('service')?->id ?? $this->input('service_id');

        return [
            // Service Name - Unique except for this service
            'name' => [
                'required',
                'string',
                'max:100',
                "unique:services,name,{$serviceId}", // Ignore current service
            ],

            // Description
            'description' => [
                'required',
                'string',
                'max:500',
            ],

            // Long Description
            'long_description' => [
                'nullable',
                'string',
            ],

            // Media URLs
            'icon_url' => [
                'nullable',
                'url',
            ],

            'image_url' => [
                'nullable',
                'url',
            ],

            // Pricing
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

            // Division
            'division_id' => [
                'required',
                'exists:divisions,id',
            ],

            // Profiles
            'profiles' => [
                'nullable',
                'array',
            ],

            'profiles.*' => [
                'exists:profiles,id',
            ],

            // Visibility Toggle
            'is_active' => [
                'nullable',
                'boolean',
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

            'is_active.boolean' => 'Active status must be true or false.',
        ];
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace
        $this->merge([
            'name' => trim($this->input('name')),
            'description' => trim($this->input('description')),
            'long_description' => trim($this->input('long_description')),
        ]);

        // Set default currency
        if (! $this->has('currency') || empty($this->input('currency'))) {
            $this->merge(['currency' => 'PKR']);
        }

        // Handle is_active boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->input('is_active') ? true : false
            ]);
        }
    }
}
