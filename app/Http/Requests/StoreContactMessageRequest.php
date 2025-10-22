<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[\d\s()+-]{7,}$/'],
            'product' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your name.',
            'email.required' => 'We need your email address to get back to you.',
            'phone.required' => 'Please include a phone number so we can reach you quickly.',
            'phone.regex' => 'Phone numbers may include digits, spaces, parentheses, or + and should be at least 7 characters.',
            'message.required' => 'Please enter a message.',
        ];
    }
}
