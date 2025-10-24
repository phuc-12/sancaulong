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
            'role_id' => $roleId
        ]);

        return back()->with('message','Đăng ký thành công');
    }

    public function login()
    {
        return view('auth.login');
    }

    // public function postLogin(LoginRequest $request)
    // {
    //     $credentials = $request->only('email','password');
    //     if(Auth::attempt($credentials))
    //     {
    //         //login thanh cong
    //         $request->session()->regenerate();
    //         $userId = Auth::id();
    //         return redirect()->intended('/');
    //     }
    //     return back()->withErrors([
    //         'email'=>'The provided credentials do not match our records'
    //     ]);
    // }

    public function postLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('trang_chu');
        }

        return back()->withErrors(['email' => 'Sai email hoặc mật khẩu']);
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
