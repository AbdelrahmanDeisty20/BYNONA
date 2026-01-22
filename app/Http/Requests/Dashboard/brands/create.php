<?php

namespace App\Http\Requests\Dashboard\brands;

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
            'name_en' => ['required', 'string', 'max:255', 'unique:brands,name_en'],
            'name_ar' => ['required', 'string', 'max:255', 'unique:brands,name_ar'],
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'], // max = 2MB
        ];
    }

    public function messages()
    {
        return [
            'name_en.required' => __('English name is required.'),
            'name_en.unique' => __('English name is already taken.'),
            'name_ar.required' => __('Arabic name is required.'),
            'name_ar.unique' => __('Arabic name is already taken.'),
            'image.required' => __('Brand image is required.'),
            'image.mimes' => __('Image must be png, jpg, jpeg, or webp.'),
            'image.max' => __('Image must not exceed 2MB.'),
        ];
    }
}