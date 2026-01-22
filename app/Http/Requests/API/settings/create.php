<?php

namespace App\Http\Requests\API\settings;

use Illuminate\Foundation\Http\FormRequest;

class create extends FormRequest
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
        'shipping' => [
            'required',
            'numeric',
            'min:0'
        ],

        'governorate_id' => [
            'required',
            'exists:governorates,id',
            'unique:settings,governorate_id'
        ],
    ];
}

    public function messages()
{
    return [
        'shipping.required' => 'Shipping is required',
        'shipping.numeric'  => 'Shipping must be a number',
        'shipping.min'      => 'Shipping must be 0 or greater',

        'governorate_id.required' => 'Governorate is required',
        'governorate_id.exists'   => 'Invalid governorate',
        'governorate_id.unique'   => 'This governorate already has shipping',
    ];
}

}