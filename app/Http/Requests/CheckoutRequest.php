<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // User must be authenticated
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}\s\-\'\.]+$/u',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{8,15}$/',
            ],
            'address' => [
                'required',
                'string',
                'max:500',
                'regex:/^[\p{L}\p{M}\p{N}\s\-\.,\/]+$/u',
            ],
            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Họ tên là bắt buộc.',
            'name.min' => 'Họ tên phải có ít nhất :min ký tự.',
            'name.max' => 'Họ tên không được vượt quá :max ký tự.',
            'name.regex' => 'Họ tên chỉ được chứa chữ cái, khoảng trắng, dấu gạch ngang, dấu nháy và dấu chấm.',

            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.regex' => 'Số điện thoại chỉ được chứa số (0-9) và phải có từ 8 đến 15 chữ số.',

            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.max' => 'Địa chỉ không được vượt quá :max ký tự.',
            'address.regex' => 'Địa chỉ chứa ký tự không hợp lệ.',

            'note.max' => 'Ghi chú không được vượt quá :max ký tự.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'họ tên',
            'phone' => 'số điện thoại',
            'address' => 'địa chỉ',
            'note' => 'ghi chú',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim and sanitize inputs
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
            'phone' => $this->phone ? preg_replace('/[^0-9]/', '', $this->phone) : null,
            'address' => $this->address ? trim($this->address) : null,
            'note' => $this->note ? trim($this->note) : null,
        ]);
    }
}
