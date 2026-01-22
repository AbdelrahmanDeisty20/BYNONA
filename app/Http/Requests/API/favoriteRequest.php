<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class favoriteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            "product_id" => [
                "required",
                "exists:products,id"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "product_id.required" => __("product is required."),
            "product_id.exists"   => __("The selected variant does not exist."),
        ];
    }
}
