<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
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
        $categoryId = $this->route('category')->id ?? $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'slug' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:500',
            'language' => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:categories,id',
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
            'name.required' => 'Tên danh mục là bắt buộc',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'parent_id.exists' => 'Danh mục cha không tồn tại',
        ];
    }
}
