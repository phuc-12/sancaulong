<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetCode;
use App\Mail\PasswordResetMail;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    // Gửi mã xác nhận
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.exists' => 'Email không tồn tại trong hệ thống',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $email = $request->email;

        // Xóa các mã cũ của email này
        PasswordResetCode::where('email', $email)->delete();

        // Tạo mã mới
        $code = PasswordResetCode::generateCode();
        $expiresInMinutes = 15;

        // Lưu mã vào database
        PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);

        // Gửi email
        try {
            Mail::to($email)->send(new PasswordResetMail($code, $expiresInMinutes));

            return response()->json([
                'success' => true,
                'message' => 'Mã xác nhận đã được gửi đến email của bạn'
            ]);
        } catch (\Exception $e) {
            // Log lỗi chi tiết
            \Log::error('Email sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi email. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Xác nhận mã
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $resetCode = PasswordResetCode::where('email', $request->email)
                                      ->where('code', $request->code)
                                      ->first();

        if (!$resetCode) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận không đúng'
            ], 422);
        }

        if ($resetCode->isExpired()) {
            $resetCode->delete();
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận đã hết hạn'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xác nhận thành công'
        ]);
    }

    // Đặt lại mật khẩu
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $resetCode = PasswordResetCode::where('email', $request->email)
                                      ->where('code', $request->code)
                                      ->first();

        if (!$resetCode) {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận không đúng'
            ], 422);
        }

        if ($resetCode->isExpired()) {
            $resetCode->delete();
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận đã hết hạn'
            ], 422);
        }

        // Cập nhật mật khẩu
        $user = Users::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa mã đã sử dụng
        $resetCode->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đặt lại mật khẩu thành công'
        ]);
    }
}