<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
class AuthController extends Controller
{
//Dang ky
    public function register()
    {
        return view('auth.register');
    }
    public function postRegister(RegisterRequest $request)
    {
        $roleType = $request->get('role_type');
        $roleId = ($roleType === 'business') ? 2 : 5;
        Users::create([
            'fullname' => $request->get('fullname'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            // 'password' => $request->get('password'),
            'role_id' => $roleId
        ]);

        return back()->with('message', 'Đăng ký thành công');
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
