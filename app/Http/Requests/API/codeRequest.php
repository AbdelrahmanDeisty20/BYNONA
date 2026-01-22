<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class codeRequest extends FormRequest
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
            "otp"=>[
                "required",
                // "exists:otps,otp",
                "numeric"
            ],
            "email"=>[
                "required",
                "email"
            ],
            
            
        ];
    }
    public function messages()
    {
        return [
            "*required"=>__("this field is required"),
            "otp.numeric"=>__("this field must be numbers"),
            "email.email"=>__('must be email')
        ];
    }
}