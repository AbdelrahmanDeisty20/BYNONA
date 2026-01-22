<?php

namespace App\Http\Requests\API\reviews;

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
            "product_id"=>["required","exists:products,id"],
            "comment"=>["required","min:3"],
            "rate"=>["required","in:1,2,3,4,5"],
        ];
    }
    public function messages()
    {
        return [
            "*required"=>__("this field is required"),
            "comment.min"=>__("must At least 3 letters"),
            "rate.in"=>__("only 5 stars "),
        ];
    }
}