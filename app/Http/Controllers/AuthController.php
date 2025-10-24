<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Users;
use Auth;
use Hash;
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
        Users::create([
            'fullname' => $request->get('fullname'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);

        return back()->with('message','Đăng ký thành công');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials))
        {
            //login thanh cong
            $request->session()->regenerate();

            return redirect()->intended('/');
        }
        return back()->withErrors([
            'email'=>'The provided credentials do not match our records'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
