<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class AuthController extends Controller
{
    //Dang ky
    public function register()
    {
        return view('auth.register');
    }

    public function postRegister(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $phone = $request->get('phone');
            $email = $request->get('email');
            $roleType = $request->get('role_type');
            $roleId = ($roleType === 'business') ? 2 : 5;

            // BƯỚC 1: Kiểm tra SĐT có tồn tại không?
            $existingUser = Users::where('phone', $phone)->first();

            if ($existingUser) {
                // BƯỚC 2: Đã có SĐT → Kiểm tra email

                if (is_null($existingUser->email) || empty($existingUser->email)) {
                    // TRƯỜNG HỢP 1: Có SĐT nhưng email = NULL
                    // → Xóa tài khoản cũ và tạo mới

                    Log::info('🔄 Tạo lại tài khoản chưa hoàn tất', [
                        'old_user_id' => $existingUser->user_id,
                        'phone' => $phone,
                        'old_email' => $existingUser->email
                    ]);

                    // Xóa tài khoản cũ
                    $existingUser->delete();

                } else {
                    // TRƯỜNG HỢP 2: Có cả SĐT và Email
                    // → Tài khoản đã tồn tại hoàn chỉnh

                    Log::warning('Tài khoản đã tồn tại', [
                        'user_id' => $existingUser->user_id,
                        'phone' => $phone,
                        'email' => $existingUser->email
                    ]);

                    DB::rollBack();

                    return back()
                        ->withInput($request->except('password', 'password_confirmation'))
                        ->withErrors([
                            'phone' => 'Số điện thoại này đã được đăng ký với email: ' .
                                $this->maskEmail($existingUser->email)
                        ]);
                }

            } else {
                // BƯỚC 3: Chưa có SĐT → Kiểm tra email

                $emailExists = Users::where('email', $email)->exists();

                if ($emailExists) {
                    DB::rollBack();

                    return back()
                        ->withInput($request->except('password', 'password_confirmation'))
                        ->withErrors(['email' => 'Email này đã được sử dụng']);
                }
            }

            $pendingData = $this->buildPendingRegistrationData($request, $roleId);

            DB::commit();

            $token = $this->storePendingRegistration($pendingData);

            $this->sendPendingVerificationMail($pendingData['email'], $pendingData['fullname'], $token);

            Log::info('Pending registration stored', [
                'email' => $email,
                'phone' => $phone,
                'token' => $token,
            ]);

            return redirect()->route('register')
                ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.')
                ->with('email', $email);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Đăng ký thất bại', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'Có lỗi xảy ra. Vui lòng thử lại sau.']);
        }
    }

    private function maskEmail($email)
    {
        if (empty($email))
            return '(ẩn)';

        $parts = explode('@', $email);
        if (count($parts) !== 2)
            return $email;

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $masked = substr($name, 0, 1) . '***';
        } else {
            $masked = substr($name, 0, 1) . '***' . substr($name, -1);
        }

        return $masked . '@' . $domain;
    }

    private function buildPendingRegistrationData(RegisterRequest $request, int $roleId): array
    {
        return [
            'fullname' => $request->get('fullname'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'password' => Hash::make($request->get('password')),
            'role_id' => $roleId,
            'status' => 1,
        ];
    }

    private function storePendingRegistration(array $data): string
    {
        $token = (string) Str::uuid();

        session()->put('pending_registration_token', $token);
        session()->put('pending_registration_data', $data);
        session()->put('pending_registration_created_at', now());

        return $token;
    }

    private function clearPendingRegistration(): void
    {
        session()->forget([
            'pending_registration_token',
            'pending_registration_data',
            'pending_registration_created_at',
        ]);
    }

    private function sendPendingVerificationMail(string $email, string $fullname, string $token): void
    {
        $verificationUrl = route('register.confirm', ['token' => $token]);
        Mail::to($email)->send(new EmailVerificationMail($verificationUrl, $fullname));
    }

    public function confirmPendingRegistration(Request $request, string $token)
    {
        $pendingToken = session('pending_registration_token');
        $pendingData = session('pending_registration_data');

        if (!$pendingToken || !$pendingData || $token !== $pendingToken) {
            return redirect()->route('register')
                ->withErrors(['error' => 'Thông tin đăng ký không hợp lệ hoặc đã hết hạn. Vui lòng đăng ký lại.']);
        }

        DB::beginTransaction();
        try {
            $user = Users::create([
                'fullname' => $pendingData['fullname'],
                'email' => $pendingData['email'],
                'phone' => $pendingData['phone'],
                'password' => $pendingData['password'],
                'role_id' => $pendingData['role_id'],
                'status' => $pendingData['status'],
                'email_verified_at' => now(),
            ]);

            DB::commit();

            // Xóa session pending
            session()->forget(['pending_registration_token', 'pending_registration_data', 'pending_registration_created_at']);

            return redirect()->route('login')->with('success', 'Xác thực email thành công! Bạn có thể đăng nhập ngay.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('register')->withErrors(['error' => 'Không thể tạo tài khoản. Vui lòng thử lại.']);
        }
    }


    public function resendPendingVerification(Request $request)
    {
        $pendingToken = session('pending_registration_token');
        $pendingData = session('pending_registration_data');

        if (!$pendingToken || !$pendingData) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin đăng ký cần xác thực. Vui lòng đăng ký lại.',
            ], 404);
        }

        $this->sendPendingVerificationMail($pendingData['email'], $pendingData['fullname'], $pendingToken);

        return response()->json([
            'success' => true,
            'message' => 'Email xác thực đã được gửi lại.',
        ]);
    }

    //Dang nhap
    public function login()
    {
        return view('auth.login');
    }

    //Dang nhap theo role
    public function postLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Kiểm tra email có tồn tại không
        $checkUser = Users::where('email', $request->email)->first();
        if (!$checkUser) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tài khoản không tồn tại']);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (is_null($user->email_verified_at)) {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'Vui lòng xác thực email trước khi đăng nhập.'])
                    ->withInput()
                    ->with('show_resend', true)
                    ->with('user_email', $user->email);
            }

            $request->session()->regenerate();

            // 🔥 KHÁCH hàng (role 5) → ưu tiên chuyển lại trang trước khi login
            if ($user->role_id == 5 && session()->has('url.intended')) {
                return redirect()->intended();
            }

            // 🔥 Các role khác → chuyển theo role
            switch ($user->role_id) {
                case 1:
                    return redirect()->route('admin.index');
                case 2:
                    return redirect()->route('owner.index');
                case 3:
                    return redirect()->route('staff.index');
                case 4:
                    return redirect()->route('manager.index');
                case 5:
                    return redirect()->route('trang_chu'); // fallback nếu không có intended
                default:
                    return redirect()->route('trang_chu');
            }
        }


        // Sai email hoặc mật khẩu
        return back()->withErrors(['email' => 'Sai email hoặc mật khẩu']);
    }



    //Dang xuat
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('trang_chu');
    }
}