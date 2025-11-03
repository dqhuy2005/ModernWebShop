<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
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
            'price_range' => 'nullable|in:under_10,10_20,20_30,over_30',
            'sort' => 'nullable|in:best_selling,newest,name_asc,name_desc,price_asc,price_desc',
            'page' => 'nullable|integer|min:1'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'price_range.in' => 'Khoảng giá không hợp lệ',
            'sort.in' => 'Tiêu chí sắp xếp không hợp lệ',
            'page.integer' => 'Trang phải là số nguyên',
            'page.min' => 'Trang phải lớn hơn 0'
        ];
    }

    /**
     * Get validated filters as array
     */
    public function getFilters(): array
    {
        return [
            'price_range' => $this->input('price_range'),
            'sort' => $this->input('sort', 'best_selling'),
        ];
    }
}
