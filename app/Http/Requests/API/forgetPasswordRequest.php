<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class forgetPasswordRequest extends FormRequest
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
                "required",
                "email",
                "exists:users,email"
            ],
        ];
    }
    public function messages()
    {
        return [
            "*required"=>__("this faild is required "),
            "email.email"=>__("email must be email"),
            "email.exists"=>__("this email is not exists")
        ];
    }
}