<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class passwordRequest extends FormRequest
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
            "email"=>[
                "email",
                "required",
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'password_confirmation' => [
                'required', 
                'string',
            ],
            "reset_token"=>["required"],
        ];
    }
    public function messages()
    {
        return [
            "*required"=>__("this field is required"),
            "email.email"=>__("must be email"),
            'password.min' => __('The new password must be at least :min characters.'),
            'password.confirmed' => __('The new password confirmation does not match.'),
        ];
    }
}