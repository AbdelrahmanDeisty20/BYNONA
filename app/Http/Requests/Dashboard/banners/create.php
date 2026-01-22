<?php

namespace App\Http\Requests\Dashboard\banners;

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
            'title_en'        => 'required|string|max:255|unique:banners,title_en',
            'title_ar'        => 'required|string|max:255|unique:banners,title_ar',
            'short_desc_en'   => 'required|string',
            'short_desc_ar'   => 'required|string',
            'image'           => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'title_en.required'      => __('English title is required.'),
            'title_en.unique'        => __('English title already exists.'),
            'title_ar.required'      => __('Arabic title is required.'),
            'title_ar.unique'        => __('Arabic title already exists.'),
            'short_desc_en.required' => __('English short description is required.'),
            'short_desc_ar.required' => __('Arabic short description is required.'),
            'image.required'         => __('Banner image is required.'),
            'image.image'            => __('File must be an image.'),
            'image.mimes'            => __('Image must be of type: jpg, jpeg, png, webp.'),
            'image.max'              => __('Image size must not exceed 4MB.'),
        ];
    }
}