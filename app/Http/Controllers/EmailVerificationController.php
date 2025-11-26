<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail; // Class gửi mail của bạn
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    // 1. Hiển thị trang thông báo (Dùng Session để hiện email)
    public function notice()
    {
        // Kiểm tra nếu không có session đăng ký chờ -> Về trang đăng ký
        if (!session()->has('pending_registration_token')) {
            return redirect()->route('register');
        }

        // Lấy data từ session để hiển thị ra view
        $pendingData = session('pending_registration_data');

        // Truyền email vào view thông qua session flash hoặc biến
        return view('auth.verify-email-notice')->with('email', $pendingData['email']);
    }

    // 2. Gửi lại email (Lấy từ Session gửi lại)
    public function resend(Request $request)
    {
        $pendingToken = session('pending_registration_token');
        $pendingData = session('pending_registration_data');

        if (!$pendingToken || !$pendingData) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng ký đã hết hạn. Vui lòng đăng ký lại.'
            ], 400);
        }

        // Tạo link verify trỏ đến route mới
        $verificationUrl = route('verification.verify', ['token' => $pendingToken]);

        try {
            // Gửi mail
            Mail::to($pendingData['email'])->send(new EmailVerificationMail($verificationUrl, $pendingData['fullname']));

            return response()->json([
                'success' => true,
                'message' => 'Email xác thực đã được gửi lại!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi mail: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. Xác thực và Tạo User thật (Logic quan trọng nhất)
    public function verify($token)
    {
        $pendingToken = session('pending_registration_token');
        $pendingData = session('pending_registration_data');

        // 1. Check Token
        if (!$pendingToken || $token !== $pendingToken) {
            return redirect()->route('register')
                ->withErrors(['error' => 'Link xác thực không hợp lệ hoặc đã hết hạn.']);
        }

        // 2. Tạo User thật vào Database (Chuyển logic tạo từ AuthController sang đây)
        DB::beginTransaction();
        try {
            $user = Users::create([
                'fullname' => $pendingData['fullname'],
                'email' => $pendingData['email'],
                'phone' => $pendingData['phone'],
                'password' => $pendingData['password'],
                'role_id' => $pendingData['role_id'],
                'status' => 1, // Active luôn
                'email_verified_at' => now(),
            ]);

            DB::commit();

            // 3. Xóa session chờ
            session()->forget(['pending_registration_token', 'pending_registration_data', 'pending_registration_created_at']);

            // 4. Đăng nhập luôn
            Auth::login($user);

            $redirectRoute = 'trang_chu'; // Mặc định là trang chủ (cho khách hàng - role 5)

            switch ($user->role_id) {
                case 1: // Admin
                    $redirectRoute = 'admin.index';
                    break;
                case 2: // Chủ sân (Doanh nghiệp)
                    $redirectRoute = 'owner.index';
                    break;
                case 3: // Nhân viên
                    $redirectRoute = 'staff.index';
                    break;
                case 4: // Quản lý
                    $redirectRoute = 'manager.index';
                    break;
                default: // Role 5 (Khách hàng) hoặc các role khác
                    $redirectRoute = 'trang_chu';
                    break;
            }

            return redirect()->route($redirectRoute)
                ->with('success', 'Xác thực email thành công! Chào mừng bạn đến với hệ thống.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('register')->withErrors(['error' => 'Lỗi hệ thống.']);
        }
    }
}