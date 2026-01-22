<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
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
            "address_id" => [
                "required",
                "exists:addresses,id"
            ],
            "name_address" => ["nullable", "string"],
            // "city" => ["nullable", "string"],
            // "phone" => ["nullable", "string"],
            "address" => ["nullable", "string"]
        ];
    }

    public function messages()
    {
        return [
            "*required" => 'this field is required',
            "*exists" => 'there is no address with this id',
        ];
    }
}