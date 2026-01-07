<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
        return [
            'q' => 'required|string|min:2|max:100',
            'price_range' => 'nullable|in:under_10,10_20,20_30,over_30',
            'sort' => 'nullable|in:best_selling,newest,name_asc,name_desc,price_asc,price_desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'q.required' => 'Vui lòng nhập từ khóa tìm kiếm',
            'q.min' => 'Từ khóa tìm kiếm phải có ít nhất 2 ký tự',
            'q.max' => 'Từ khóa tìm kiếm không được quá 100 ký tự',
            'price_range.in' => 'Khoảng giá không hợp lệ',
            'sort.in' => 'Cách sắp xếp không hợp lệ',
            'per_page.integer' => 'Số lượng sản phẩm phải là số nguyên',
            'per_page.min' => 'Số lượng sản phẩm tối thiểu là 1',
            'per_page.max' => 'Số lượng sản phẩm tối đa là 100',
        ];
    }

    /**
     * Get validated search keyword
     */
    public function getKeyword(): string
    {
        return trim($this->validated()['q']);
    }

    /**
     * Get validated per page value
     */
    public function getPerPage(): int
    {
        return min((int) $this->input('per_page', 12), 100);
    }

    /**
     * Get validated filters
     */
    public function getFilters(): array
    {
        return [
            'price_range' => $this->input('price_range', ''),
            'sort' => $this->input('sort', 'best_selling')
        ];
    }
}
