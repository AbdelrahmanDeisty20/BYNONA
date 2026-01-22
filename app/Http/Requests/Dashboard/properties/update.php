<?php

namespace App\Http\Requests\Dashboard\properties;

use Illuminate\Foundation\Http\FormRequest;

class update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // خليها true لو كل المستخدمين المصرح لهم يقدروا يعدل
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key_en'   => ['required', 'string', 'max:255'],
            'key_ar'   => ['required', 'string', 'max:255'],
            'value_en' => ['required', 'string', 'max:255'],
            'value_ar' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Custom messages for validation.
     */
    public function messages(): array
    {
        return [
            'key_en.required'   => __('English key is required.'),
            'key_ar.required'   => __('Arabic key is required.'),
            'value_en.required' => __('English value is required.'),
            'value_ar.required' => __('Arabic value is required.'),
        ];
    }
}