<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'role_type' => ['required', 'string', 'in:customer,business'],
            'fullname' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'password' => 'required|string|confirmed|min:8',
            'phone' => [
                'required',
                'string',
                'regex:/^(0[3|5|7|8|9])[0-9]{8}$/', // Số VN: 03x, 05x, 07x, 08x, 09x + 8 số
            ]
        ];
    }
    public function messages(): array
    {
        return [
            // Role type
            'role_type.required' => 'Vui lòng chọn loại tài khoản',
            'role_type.in' => 'Loại tài khoản không hợp lệ',
            
            // Fullname
            'fullname.required' => 'Vui lòng nhập tên đầy đủ',
            'fullname.min' => 'Tên phải có ít nhất 2 ký tự',
            'fullname.max' => 'Tên không được vượt quá 100 ký tự',
            
            // Email
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            
            // Phone
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'phone.regex' => 'Số điện thoại không đúng định dạng (VD: 0912345678)',
            
            // Password
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
        ];
    }
}
