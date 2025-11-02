<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http.Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles 
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // Kiểm tra xem user có relationship 'role' không
        if (!$user->role) {
            abort(403, 'Lỗi: Người dùng không có vai trò.');
        }

        // Lấy tên vai trò từ CSDL
        $userRoleName = $user->role->role_name;

        // Kiểm tra vai trò
        if (in_array($userRoleName, $roles)) {
            return $next($request); // Cho phép request đi tiếp
        }

        abort(403, 'Bạn không có quyền truy cập vào trang này.');
    }
}