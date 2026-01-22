<?php

namespace App\Http\Requests\Dashboard\users;

use Illuminate\Foundation\Http\FormRequest;

class create extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'user_type' => 'required|in:user,admin,subAdmin',
            'password' => 'required|string|min:6'
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('First name is required.'),
            'last_name.required' => __('Last name is required.'),
            'email.required' => __('Email is required.'),
            'email.email' => __('Email format is invalid.'),
            'email.unique' => __('Email already exists.'),
            'phone.required' => __('Phone is required.'),
            'user_type.required' => __('User type is required.'),
            'user_type.in' => __('Invalid user type.'),
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 6 characters.'),
        ];
    }
}
