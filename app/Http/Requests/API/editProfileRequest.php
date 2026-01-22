<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class editProfileRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'first_name' => [
                'nullable',
                // Rule::unique('users', 'first_name')->ignore($userId),
            ],
            'last_name' => [
                'nullable',
                // Rule::unique('users', 'last_name')->ignore($userId),
            ],
            'phone' => [
                'nullable',
                'numeric',
                // Rule::unique('users', 'phone')->ignore($userId),
            ],
            'email' => [
                'nullable',
                'email',
                // Rule::unique('users', 'email')->ignore($userId),
            ],

            'current_password' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !Hash::check($value, auth()->user()->password)) {
                        $fail(__('The current password is incorrect.'));
                    }
                },
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'password_confirmation' => [
                'nullable', 
                'string',
            ],
        ];
    }

    /**
     * Custom messages for validation
     */
    public function messages(): array
    {
        return [
            "*required"=>__("this field is required"),
            "email.email"=>__(""),
            // 'first_name.unique' => __('This first name is already taken.'),
            // 'last_name.unique' => __('This last name is already taken.'),
            'phone.unique' => __('This phone number is already taken.'),
            'password.min' => __('The new password must be at least :min characters.'),
            'password.confirmed' => __('The new password confirmation does not match.'),
        ];
    }
}