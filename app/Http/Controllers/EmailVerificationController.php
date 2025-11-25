<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class EmailVerificationController extends Controller
{
    // Hiển thị trang thông báo xác thực email
    public function notice()
    {
        return view('auth.verify-email-notice');
    }

    // Gửi lại email xác thực
    public function resend(Request $request)
    {
        $user = Users::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email không tồn tại trong hệ thống'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email này đã được xác thực rồi'
            ], 400);
        }

        $this->sendVerificationEmail($user);

        return response()->json([
            'success' => true,
            'message' => 'Email xác thực đã được gửi lại'
        ]);
    }

    // Xác thực email
    public function verify(Request $request, $id, $hash)
    {
        $user = Users::where('user_id', $id)->firstOrFail();

        // Kiểm tra hash có đúng không
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Link xác thực không hợp lệ');
        }

        // Kiểm tra đã xác thực chưa
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Email đã được xác thực trước đó');
        }

        // Kiểm tra signature (thời gian hết hạn)
        if (!$request->hasValidSignature()) {
            return redirect()->route('login')->with('error', 'Link xác thực đã hết hạn. Vui lòng yêu cầu gửi lại email xác thực');
        }

        // Xác thực email
        $user->markEmailAsVerified();

        return redirect()->route('login')->with('success', 'Xác thực email thành công! Bạn có thể đăng nhập ngay.');
    }

    // Hàm gửi email xác thực
    public static function sendVerificationEmail($user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60), // Hết hạn sau 60 phút
            [
                'id' => $user->user_id,
                'hash' => sha1($user->getEmailForVerification())
            ]
        );

        try {
            Mail::to($user->email)->send(new EmailVerificationMail($verificationUrl, $user->fullname));
            return true;
        } catch (\Exception $e) {
            \Log::error('Email verification sending failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->user_id
            ]);
            return false;
        }
    }
}