<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
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
        'first_name' => ['required', 'string', 'max:50'],
        'last_name'  => ['required', 'string', 'max:50'],
        'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
        'phone'      => ['required', 'digits_between:10,15'],
        'password'   => ['required', 'min:6'],
    ];
}

public function messages()
{
    return [
        'first_name.required' => __('First name is required.'),
        'last_name.required'  => __('Last name is required.'),
        'email.required'      => __('Email is required.'),
        'email.email'         => __('Email is invalid.'),
        'email.unique'        => __('Email is already taken.'),
        'phone.required'      => __('Phone is required.'),
        'phone.digits_between'=> __('Phone must be between 10 and 15 digits.'),
        'password.required'   => __('Password is required.'),
        'password.min'        => __('Password must be at least :min characters.', ['min' => 6]),
    ];
}

}