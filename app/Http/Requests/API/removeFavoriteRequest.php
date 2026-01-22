<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class removeFavoriteRequest extends FormRequest
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
            'product_id' => [
                'required',
                'exists:products,id' // اتأكدنا من صحة الـ Property ID
            ],
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => __("property_id is required"),
            'product_id.exists'   => __("The selected property does not exist"),
        ];
    }
}
