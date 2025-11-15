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

                    Log::info('🔄 Recreating incomplete account', [
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
                    ]);

                    DB::commit();

                    Log::info('New account created', [
                        'new_user_id' => $newUser->user_id,
                        'email' => $email,
                        'phone' => $phone
                    ]);

                    return redirect()->route('login')
                        ->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');

                } else {
                    // TRƯỜNG HỢP 2: Có cả SĐT và Email
                    // → Tài khoản đã tồn tại hoàn chỉnh

                    Log::warning('Account already exists', [
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
                ]);

                DB::commit();

                Log::info('New account created successfully', [
                    'user_id' => $newUser->user_id,
                    'email' => $email,
                    'phone' => $phone,
                    'role_id' => $roleId
                ]);

                return redirect()->route('login')
                    ->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Registration failed', [
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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 1 = admin
            // 2 = owner (Chủ sân)
            // 3 = staff (Nhân viên)
            // 4 = manager (Quản lý sân)
            // 5 = customer (Khách hàng)

            switch ($user->role_id) {
                case 1:
                    // Admin
                    return redirect()->route('admin.index');
                case 2:
                    // Chủ sân (Owner)
                    return redirect()->route('owner.index');
                case 3:
                    // Nhân viên (Staff)
                    return redirect()->route('staff.index');
                case 4:
                    // Quản lý sân (Manager)
                    return redirect()->route('manager.index');
                case 5:
                    // Khách hàng (Customer)
                    return redirect()->route('trang_chu');
                default:
                    // Mặc định (ví dụ: vai trò không xác định)
                    return redirect()->route('trang_chu');
            }
        }

        return back()->withErrors(['email' => 'Sai email hoặc mật khẩu']);
    }

    //Dang xuat
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
