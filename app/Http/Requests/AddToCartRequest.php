<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
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
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                'min:1',
            ],
            'quantity' => [
                'nullable',
                'integer',
                'min:1',
                'max:999', // Prevent abuse
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Sản phẩm là bắt buộc.',
            'product_id.integer' => 'ID sản phẩm không hợp lệ.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'product_id.min' => 'ID sản phẩm không hợp lệ.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            'quantity.max' => 'Số lượng không được vượt quá 999.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('quantity') || $this->quantity === null) {
            $this->merge(['quantity' => 1]);
        }

        if ($this->has('product_id')) {
            $this->merge(['product_id' => (int) $this->product_id]);
        }
    }
}
