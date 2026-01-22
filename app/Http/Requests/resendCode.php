<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class resendCode extends FormRequest
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
                "exists:users,email",
                "required"
            ],
        ];
    }
    public function messages(){
        return [
            "email.required"=>"email is required",
            "email.email"=>"must be email"
        ];
    }
}