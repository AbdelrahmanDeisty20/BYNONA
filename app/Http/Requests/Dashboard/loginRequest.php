<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class loginRequest extends FormRequest
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
        return [
            'email'=>['email','exists:users,email','required'],
            'password'=>['required'],
        ];
        
    }
    public function messages()
    {
        return [
            'email.required' => __('Email is required.'),
            'email.email'    => __('Must be a valid email address.'),
            'email.exists'   => __('This email is not registered.'),
            'password.required' => __('Password is required.'),
        ];
    }
    
}