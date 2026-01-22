<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class profileUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();
        return [
            'first_name' => ['nullable', 'min:3'],
            'last_name' => ['nullable', 'min:3'],
            'phone' => ['nullable', 'regex:/^01[0-9]{9}$/'],
            'email' => ['nullable', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.min' => __('First name must be at least 3 characters.'),
            'last_name.min' => __('Last name must be at least 3 characters.'),
            'phone.regex' => __('Phone number must be 11 digits and start with 01.'),
            'phone.unique' => __('This phone number is already in use.'),
            'email.email' => __('Please enter a valid email address.'),
            'email.unique' => __('This email is already in use.'),
        ];
    }
}
