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
        // 1. Kiểm tra xem user đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect('login'); // Nếu chưa, về trang login
        }

        // 2. Lấy thông tin user
        $user = Auth::user();

        // 3. Kiểm tra xem user có vai trò (relationship 'role') không
        // (Định nghĩa relationship 'role' ở Bước 2.2)
        if (!$user->role) {
             abort(403, 'Lỗi: Người dùng không có vai trò.');
        }

        // 4. Lấy tên vai trò từ CSDL (ví dụ: 'staff', 'admin')
        $userRoleName = $user->role->role_name;

        // 5. Kiểm tra xem tên vai trò của user có nằm trong danh sách $roles được yêu cầu không
        if (in_array($userRoleName, $roles)) {
            // Nếu có, cho phép request đi tiếp
            return $next($request);
        }

        // 6. Nếu không có quyền, báo lỗi 403 (Forbidden)
        abort(403, 'Bạn không có quyền truy cập vào trang này.');
    }
}