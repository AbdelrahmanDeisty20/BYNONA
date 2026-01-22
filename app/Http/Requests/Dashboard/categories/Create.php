<?php

namespace App\Http\Requests\Dashboard\categories;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
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
            'name_ar' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name_ar',
            ],

            'name_en' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name_en',
            ],

            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=200,min_height=200',
            ],
            'parent_id' => 'nullable|exists:categories,id',

        ];
    }

    public function messages()
    {
        return [
            'name_ar.required' => __('Arabic name is required.'),
            'name_ar.unique' => __('Arabic name already exists.'),
            'name_ar.max' => __('Arabic name must not exceed 255 characters.'),

            'name_en.required' => __('English name is required.'),
            'name_en.unique' => __('English name already exists.'),
            'name_en.max' => __('English name must not exceed 255 characters.'),

            'image.required' => __('Image is required.'),
            'image.image' => __('File must be an actual image.'),
            'image.mimes' => __('Image must be one of: jpg, jpeg, png, webp.'),
            'image.max' => __('Image size must not exceed 2MB.'),
            'image.dimensions' => __('Image dimensions are too small. Minimum size is 200x200 pixels.'),
        ];
    }
}
