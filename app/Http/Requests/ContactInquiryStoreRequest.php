<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactInquiryStoreRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $this->merge(['phone' => \App\Support\LeadPhone::normalize($this->input('phone'))]);
        }
        if ($this->filled('division_id') && ! $this->filled('assigned_to')) {
            $recipients = \App\Models\User::publicInquiryRecipients()->where('division_id', $this->input('division_id'))->pluck('id');
            if ($recipients->count() === 1) {
                $this->merge(['assigned_to' => $recipients->first()]);
            }
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'division_id' => ['required', 'integer', 'exists:divisions,id'],
            'assigned_to' => ['required', 'integer', \Illuminate\Validation\Rule::in(
                \App\Models\User::publicInquiryRecipients()->where('division_id', $this->input('division_id'))->pluck('id')->all()
            )],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'division_id.required' => 'Please choose a department.',
            'assigned_to.required' => 'Please choose a specialist.',
            'assigned_to.in' => 'Choose an available specialist in this department.',
            'name.required' => 'Please provide your name.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Please provide your phone number.',
            'phone.regex' => 'Please provide a valid phone number (e.g., +923001234567).',
            'subject.required' => 'Please provide a subject for your inquiry.',
            'subject.max' => 'Subject must not exceed 200 characters.',
            'message.required' => 'Please provide your message.',
            'message.min' => 'Message must be at least 10 characters.',
            'message.max' => 'Message must not exceed 5000 characters.',
        ];
    }
}
