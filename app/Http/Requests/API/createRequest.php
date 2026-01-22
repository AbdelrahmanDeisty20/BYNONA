<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class createRequest extends FormRequest
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
            'property_id' => ['required', 'exists:properties,id'],
            // "address_id"=>["nullable"],
            'quantity' => ['required','integer','min:1'],
            // 'type' => ['required', Rule::in(['retail','wholesale'])],
        ];
    }

    public function messages()
    {
        return [
            '*.required' => __("هذا الحقل مطلوب"),
        ];
    }
}