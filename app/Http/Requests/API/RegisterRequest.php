<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'min:3',
                'regex:/^[\p{L}\s]+$/u',
            ],

            'last_name' => [
                'required',
                'min:3',
                'regex:/^[\p{L}\s]+$/u',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],
            "user_type"=>[
                "nullable"
            ],

            'phone' => [
                'required',
                'numeric',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'password_confirmation' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('messages.first_name.required'),
            'first_name.min' => __('messages.first_name.min'),
            'first_name.regex' => __('messages.first_name.regex'),

            'last_name.required' => __('messages.last_name.required'),
            'last_name.min' => __('messages.last_name.min'),
            'last_name.regex' => __('messages.last_name.regex'),

            'email.required' => __('messages.email.required'),
            'email.email' => __('messages.email.email'),
            'email.unique' => __('messages.email.unique'),

            'phone.required' => __('messages.phone.required'),
            'phone.numeric' => __('messages.phone.numeric'),

            'password.required' => __('messages.password.required'),
            'password.confirmed' => __('messages.password.confirmed'),
            'password.min' => __('messages.password.min'),
            'password_confirmation.required' => __('messages.password_confirmation.required'),
        ];
    }
}