<?php

namespace App\Http\Requests\Dashboard\brands;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class edit extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name_en')->ignore($this->route('id'))
            ],
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name_ar')->ignore($this->route('id'))
            ],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name_en.required' => __('English name is required.'),
            'name_en.max'      => __('English name must not exceed 255 characters.'),
            'name_en.unique'   => __('This English name already exists.'),

            'name_ar.required' => __('Arabic name is required.'),
            'name_ar.max'      => __('Arabic name must not exceed 255 characters.'),
            'name_ar.unique'   => __('This Arabic name already exists.'),

            'image.image'      => __('File must be an image.'),
            'image.mimes'      => __('Allowed image formats: jpg, jpeg, png, webp.'),
            'image.max'        => __('Image must not exceed 2MB.'),
        ];
    }
}