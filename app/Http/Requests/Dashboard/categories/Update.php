<?php

namespace App\Http\Requests\Dashboard\categories;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name_ar')->ignore($this->id),
            ],

            'name_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name_en')->ignore($this->id),
            ],

            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
                Rule::notIn([$this->category]), // ممنوع يبقى parent لنفسه
            ],

            'image' => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => __('Arabic name is required.'),
            'name_ar.string'   => __('Arabic name must be a string.'),
            'name_ar.max'      => __('Arabic name must not exceed 255 characters.'),
            'name_ar.unique'   => __('Arabic name already exists.'),

            'name_en.required' => __('English name is required.'),
            'name_en.string'   => __('English name must be a string.'),
            'name_en.max'      => __('English name must not exceed 255 characters.'),
            'name_en.unique'   => __('English name already exists.'),

            'parent_id.integer' => __('Selected parent category is invalid.'),
            'parent_id.exists'  => __('Selected parent category does not exist.'),
            'parent_id.not_in'  => __('A category cannot be its own parent.'),

            'image.image' => __('File must be an image.'),
            'image.max'   => __('Image size must not exceed 2MB.'),
        ];
    }
}