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
use App\Http\Controllers\EmailVerificationController;

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

                    // Tạo tài khoản mới
                    $newUser = Users::create([
                        'fullname' => $request->get('fullname'),
                        'email' => $email,
                        'phone' => $phone,
                        'password' => Hash::make($request->get('password')),
                        'role_id' => $roleId,
                        'status' => 1,
                        'email_verified_at' => null, // Chưa xác thực
                    ]);

                    // Gửi email xác thực
                    EmailVerificationController::sendVerificationEmail($newUser);

                    DB::commit();

                    Log::info('New account created', [
                        'new_user_id' => $newUser->user_id,
                        'email' => $email,
                        'phone' => $phone
                    ]);

                    return redirect()->route('verification.notice')
                        ->with('email', $email)
                        ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');

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

                // TRƯỜNG HỢP 3: Không có SĐT, không có Email
                // → Tạo tài khoản mới

                $newUser = Users::create([
                    'fullname' => $request->get('fullname'),
                    'email' => $email,
                    'phone' => $phone,
                    'password' => Hash::make($request->get('password')),
                    'role_id' => $roleId,
                    'status' => 1,
                    'email_verified_at' => null, // Chưa xác thực
                ]);

                // Gửi email xác thực
                EmailVerificationController::sendVerificationEmail($newUser);

                DB::commit();

                Log::info('Tài khoản mới đã được tạo thành công', [
                    'user_id' => $newUser->user_id,
                    'email' => $email,
                    'phone' => $phone,
                    'role_id' => $roleId
                ]);

                return redirect()->route('verification.notice')
                    ->with('email', $email)
                    ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');
            }

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
                return back()->withErrors([
                    'email' => 'Vui lòng xác thực email trước khi đăng nhập.'
                ])->withInput()->with('show_resend', true)->with('user_email', $user->email);
            }

            $request->session()->regenerate();

            // 🔥 Ưu tiên chuyển lại trang trước khi login
            if (session()->has('url.intended')) {
                return redirect()->intended();
            }

            // Nếu không có intended thì mới redirect theo role
            switch ($user->role_id) {
                case 1: return redirect()->route('admin.index');
                case 2: return redirect()->route('owner.index');
                case 3: return redirect()->route('staff.index');
                case 4: return redirect()->route('manager.index');
                case 5: return redirect()->route('trang_chu');
                default: return redirect()->route('trang_chu');
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