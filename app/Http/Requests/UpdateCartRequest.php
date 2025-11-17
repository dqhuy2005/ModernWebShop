<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
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
            'cart_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cart_id.required' => 'ID giỏ hàng là bắt buộc.',
            'cart_id.integer' => 'ID giỏ hàng không hợp lệ.',
            'cart_id.min' => 'ID giỏ hàng không hợp lệ.',
            'quantity.required' => 'Số lượng là bắt buộc.',
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
        if ($this->has('cart_id')) {
            $this->merge(['cart_id' => (int) $this->cart_id]);
        }
        if ($this->has('quantity')) {
            $this->merge(['quantity' => (int) $this->quantity]);
        }
    }
}
