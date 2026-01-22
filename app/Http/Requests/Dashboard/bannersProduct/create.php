<?php

namespace App\Http\Requests\Dashboard\bannersProduct;

use Illuminate\Foundation\Http\FormRequest;

class Create extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // مهم جداً
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],

            'desc_en'  => ['required', 'string'],
            'desc_ar'  => ['required', 'string'],

            'price'    => ['required', 'numeric', 'min:0'],

            'image'    => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'title_en.required' => __('English title is required.'),
            'title_ar.required' => __('Arabic title is required.'),

            'desc_en.required' => __('English description is required.'),
            'desc_ar.required' => __('Arabic description is required.'),

            'price.required'   => __('Price is required.'),
            'price.numeric'    => __('Price must be a valid number.'),

            'image.required'   => __('Banner image is required.'),
            'image.image'      => __('File must be an image.'),
            'image.mimes'      => __('Allowed image formats: jpg, jpeg, png, webp.'),
        ];
    }
}