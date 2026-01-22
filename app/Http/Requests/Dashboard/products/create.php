<?php

namespace App\Http\Requests\Dashboard\products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class create extends FormRequest
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
                Rule::unique('products')->where(function ($query) {
                    return $query->where('type', $this->type);
                })
            ],
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('type', $this->type);
                })
            ],
            'desc_ar' => 'required',
            'desc_en' => 'required',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:wholesale,retail',
            'variants' => 'required|array|min:1',
            'variants.*.retail_price' => $this->type === 'retail' ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'variants.*.wholesale_price' => $this->type === 'wholesale' ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'variants.*.min_quantity' => $this->type === 'wholesale' ? 'required|integer|min:1' : 'nullable|integer|min:1',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.main_image_file' => 'required|image|max:2048',
            'variants.*.gallery_files' => 'required|array|min:1',
            'variants.*.gallery_files.*' => 'image|max:2048',
            'variants.*.attributes' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    $keys = collect($value)->pluck('key_en')->filter();
                    if ($keys->count() !== $keys->unique()->count()) {
                        $fail(__('Each variant must have unique attribute keys.'));
                    }
                }
            ],
            'variants.*.attributes.*.key_en' => 'required|string|max:255',
            'variants.*.attributes.*.key_ar' => 'required|string|max:255',
            'variants.*.attributes.*.value_en' => 'required|string|max:255',
            'variants.*.attributes.*.value_ar' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name_en.required' => __('This field is required'),
            'name_en.unique' => __('The English name has already been taken for this product type.'),
            'name_ar.required' => __('This field is required'),
            'name_ar.unique' => __('The Arabic name has already been taken for this product type.'),
            'desc_ar.required' => __('This field is required'),
            'desc_en.required' => __('This field is required'),
            'brand_id.required' => __('This field is required'),
            'brand_id.exists' => __('Selected brand does not exist.'),
            'category_id.required' => __('This field is required'),
            'category_id.exists' => __('Selected category does not exist.'),
            'variants.*.retail_price.required' => __('This field is required'),
            'variants.*.wholesale_price.required' => __('This field is required'),
            'variants.*.stock.required' => __('This field is required'),
            'variants.*.main_image_file.required' => __('This field is required'),
            'variants.*.gallery_files.required' => __('This field is required'),
            'variants.*.attributes.*.key_en.required' => __('This field is required'),
            'variants.*.attributes.*.key_ar.required' => __('This field is required'),
            'variants.*.attributes.*.value_en.required' => __('This field is required'),
            'variants.*.attributes.*.value_ar.required' => __('This field is required'),
        ];
    }
}
