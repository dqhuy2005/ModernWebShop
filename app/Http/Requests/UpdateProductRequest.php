<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('id');

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'price' => 'required|integer|min:0|max:999999999',
            'currency' => 'nullable|string|max:10',
            'images' => 'nullable|string',
            'status' => 'nullable|boolean',
            'is_hot' => 'nullable|boolean',
            'language' => 'nullable|string|max:10',
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.required' => 'Slug là bắt buộc',
            'price.required' => 'Giá sản phẩm là bắt buộc',
            'price.integer' => 'Giá phải là số nguyên',
            'price.min' => 'Giá phải là số dương',
            'price.max' => 'Giá vượt quá giới hạn cho phép (999.999.999 ₫)',
        ];
    }
}
