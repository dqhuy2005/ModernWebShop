<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Fullname: required, string, max 255 characters
            'fullname' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}\s\-\'\.]+$/u', // Unicode letters, spaces, hyphens, apostrophes, dots
            ],

            // Email: required, valid format, unique, gmail standard
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9][a-zA-Z0-9._+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Standard email format
            ],

            // Password: required, min 6 chars, with special chars, no dangerous characters
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
                'confirmed',
                // Custom password rules
                'regex:/^[a-zA-Z0-9@#$%^&*()_+\-=\[\]{}|:";,.<>?\/]+$/', // Allowed characters only
                'regex:/[0-9]/', // Must contain at least one number
                'regex:/[a-zA-Z]/', // Must contain at least one letter
                'regex:/[@#$%^&*()_+\-=\[\]{}|:";,.<>?\/]/', // Must contain at least one special character
            ],

            // Phone: optional, numeric only, 8-15 digits
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{8,15}$/', // Only numbers, 8-15 digits
            ],

            // Birthday: optional, valid date, not future date, reasonable age
            'birthday' => [
                'nullable',
                'date',
                'before:today', // Cannot be today or future
                'after:1900-01-01', // Reasonable minimum year
                'before_or_equal:' . now()->subYears(13)->format('Y-m-d'), // Must be at least 13 years old
            ],

            // Role: required, must exist in roles table
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            // Image: optional, valid image file
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048', // 2MB max
                'dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000', // Reasonable dimensions
            ],

            // Status: optional, boolean
            'status' => [
                'nullable',
                'boolean',
            ],

            // Language: optional, specific values only
            'language' => [
                'nullable',
                'string',
                'in:vi,en', // Only Vietnamese or English
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Họ tên là bắt buộc.',
            'fullname.min' => 'Họ tên phải có ít nhất :min ký tự.',
            'fullname.max' => 'Họ tên không được vượt quá :max ký tự.',
            'fullname.regex' => 'Họ tên chỉ được chứa chữ cái, khoảng trắng, dấu gạch ngang, dấu nháy và dấu chấm.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'email.max' => 'Email không được vượt quá :max ký tự.',
            'email.regex' => 'Email phải có định dạng chuẩn (vd: user@gmail.com).',

            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá :max ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ cái, 1 số và 1 ký tự đặc biệt (@#$%^&*...).',

            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.regex' => 'Số điện thoại chỉ được phép nhập số (0-9) và phải có từ 8 đến 15 chữ số.',

            'birthday.date' => 'Ngày sinh không hợp lệ.',
            'birthday.before' => 'Ngày sinh không được là ngày hôm nay hoặc tương lai.',
            'birthday.after' => 'Ngày sinh không hợp lệ (quá xa trong quá khứ).',
            'birthday.before_or_equal' => 'Người dùng phải từ 13 tuổi trở lên.',

            'role_id.required' => 'Vai trò là bắt buộc.',
            'role_id.exists' => 'Vai trò được chọn không tồn tại.',

            'image.image' => 'File phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, hoặc webp.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'image.dimensions' => 'Kích thước hình ảnh phải từ 50x50px đến 2000x2000px.',
        ];
    }

    public function attributes(): array
    {
        return [
            'fullname' => 'họ tên',
            'email' => 'email',
            'password' => 'mật khẩu',
            'phone' => 'số điện thoại',
            'birthday' => 'ngày sinh',
            'role_id' => 'vai trò',
            'image' => 'hình ảnh',
            'status' => 'trạng thái',
            'language' => 'ngôn ngữ',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fullname' => $this->fullname ? trim($this->fullname) : null,
            'email' => $this->email ? trim(strtolower($this->email)) : null,
            'phone' => $this->phone ? preg_replace('/[^0-9]/', '', $this->phone) : null, // Remove non-numeric
        ]);
    }
}
