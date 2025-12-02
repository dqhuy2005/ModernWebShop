<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{10,11}$/',
            ],
            'address' => [
                'required',
                'string',
            ],
            'birthday' => [
                'required',
                'date',
                'before:today',
                'after:1940-01-01',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:1024',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Fullname messages
            'fullname.required' => 'Họ và tên là bắt buộc.',
            'fullname.string' => 'Họ và tên phải là chuỗi ký tự.',
            'fullname.min' => 'Họ và tên phải có ít nhất :min ký tự.',
            'fullname.max' => 'Họ và tên không được vượt quá :max ký tự.',

            // Phone messages
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.regex' => 'Số điện thoại phải có 10-11 chữ số.',

            // Address messages
            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',

            // Birthday messages
            'birthday.required' => 'Ngày sinh là bắt buộc.',
            'birthday.date' => 'Ngày sinh phải là ngày hợp lệ.',
            'birthday.before' => 'Ngày sinh phải trước ngày hôm nay.',
            'birthday.after' => 'Ngày sinh không hợp lệ.',

            // Image messages
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'fullname' => 'họ và tên',
            'phone' => 'số điện thoại',
            'address' => 'địa chỉ',
            'birthday' => 'ngày sinh',
            'image' => 'hình ảnh',
        ];
    }
}
