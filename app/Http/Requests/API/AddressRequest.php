<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'name_address' => [
                'required',
            ],
            'address' => ['required'],
            // 'city' => ['required'],
            // 'phone' => [
            //     'required',
            //     'regex:/^\+20(10|11|12|15)[0-9]{8}$/',
            // ],
        ];
    }

    public function messages()
    {
        return [
            "*required" => __("this field is required"),
            ];
    }
}