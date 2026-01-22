<?php

namespace App\Http\Requests\Dashboard\banners;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class update extends FormRequest
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
        $id = $this->route('id'); // لو مسميه banner في الروت

        return [
            'title_en' => [
                'required',
                'max:255',
                Rule::unique('banners', 'title_en')->ignore($id),
            ],

            'title_ar' => [
                'required',
                'max:255',
                Rule::unique('banners', 'title_ar')->ignore($id),
            ],

            'short_desc_en' => ['required', 'max:500'],
            'short_desc_ar' => ['required', 'max:500'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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
            'image.image'            => __('File must be an image.'),
            'image.mimes'            => __('Image must be of type: jpg, jpeg, png, webp.'),
            'image.max'              => __('Image size must not exceed 2MB.'),
        ];
    }
}