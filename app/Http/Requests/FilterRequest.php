<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|integer|exists:categories,id',
            'brand_id' => 'nullable|integer|exists:brands,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'attribute_ids' => 'nullable|array',
            'attribute_ids.*' => 'nullable|integer|exists:attributes,id',
            'sort' => 'nullable|in:popular,a_z,z_a,low_high,high_low,latest,offers',
            'page' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => __('selected category not found'),
            'brand_id.exists' => __('selected brand not found'),
            'brand.string' => __('brand name must be string'),
            'brand.max' => __('brand name is too long'),
            'min_price.numeric' => __('min price must be number'),
            'min_price.min' => __('min price cannot be less than zero'),
            'max_price.numeric' => __('max price must be number'),
            'max_price.min' => __('max price cannot be less than zero'),
            'attribute_ids.array' => __('attributes must be an array'),
            'attribute_ids.*.integer' => __('each attribute id must be an integer'),
            'attribute_ids.*.exists' => __('attribute id must exist in attributes table'),
            'sort.in' => __('invalid sort option'),
        ];
    }
}
