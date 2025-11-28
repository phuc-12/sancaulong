<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class ProfileController extends Controller
{
    //Hiển thị form sửa cho user đang đăng nhập
    public function edit(Request $request, $id) // <-- THÊM $id
    {
        if ((int)$id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa hồ sơ này.');
        }

        // Lấy thông tin user đang đăng nhập
        $user = Auth::user(); 

        return view('profile.edit', compact('user'));
    }

    /**
     * Cập nhật hồ sơ của user đang đăng nhập.
     */
    public function update(Request $request, $id)
    {
        if ((int)$id !== Auth::id()) {
            abort(403, 'Bạn không có quyền cập nhật hồ sơ này.');
        }
        $user = Auth::user();

        // --- VALIDATION (Xác thực dữ liệu) ---
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:100',
            'phone' => 'nullable|string|min:10',
            'address' => 'nullable|string|max:255',
            'CCCD' => ['nullable', 'string', 'max:50', Rule::unique('users', 'CCCD')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // --- XỬ LÝ UPLOAD AVATAR ---
        $avatarPath = $user->avatar; // Giữ avatar cũ làm mặc định
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // Tạo tên file mới duy nhất
            $extension = $file->getClientOriginalExtension();
            $newFileName = 'avatar_' . $user->user_id . '_' . time() . '.' . $extension;
            
            // Thư mục lưu (ví dụ: public/img/profiles)
            $destinationPath = public_path('img/profiles');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            try {
                // Di chuyển file mới vào
                $file->move($destinationPath, $newFileName);
                $avatarPath = 'img/profiles/' . $newFileName; // Đường dẫn tương đối để lưu vào CSDL

                // (Quan trọng) Xóa avatar cũ nếu tồn tại
                if ($user->avatar && $user->avatar != 'img/profiles/avatar-05.jpg' && file_exists(public_path($user->avatar))) {
                     unlink(public_path($user->avatar));
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi upload avatar: ' . $e->getMessage());
                return back()->withInput()->withErrors(['avatar' => 'Không thể tải lên ảnh đại diện.']);
            }
        }

        // --- CẬP NHẬT DỮ LIỆU USER ---
        $user->fullname = $validatedData['fullname'];
        $user->email = $validatedData['email'];
        $user->phone = $validatedData['phone'];
        $user->address = $validatedData['address'];
        $user->CCCD = $validatedData['CCCD'];
        $user->avatar = $avatarPath; // Gán đường dẫn avatar mới (hoặc cũ)

        // Chỉ cập nhật mật khẩu nếu người dùng nhập mật khẩu mới
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Lưu thay đổi vào CSDL
        $user->save();

        // --- PHẢN HỒI ---
        // Quay lại trang profile với thông báo thành công
        return redirect()->route('user.profile', ['id' => $user->user_id])
                         ->with('success', 'Cập nhật hồ sơ thành công!');
    }
}
