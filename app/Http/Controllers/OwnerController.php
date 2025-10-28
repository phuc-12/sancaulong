<?php

namespace App\Http\Controllers;

// --- SỬA LẠI USE STATEMENTS ---
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // Sử dụng Facade đầy đủ
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OwnerController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        if (!$owner) {
            abort(401, 'Unauthorized');
        }
        $facility = Facility::withoutGlobalScopes()
            ->where('owner_id', $owner->user_id)
            ->first();

        $facilityStatusMessage = null;
        $facilityStatusType = 'info';

        if ($facility) {
            if ($facility->status == 'chờ duyệt') {
                $facilityStatusMessage = 'Thông tin cơ sở của bạn đang chờ quản trị viên phê duyệt.';
                $facilityStatusType = 'warning';
            } elseif ($facility->status == 'đã duyệt') {
                $facilityStatusMessage = 'Cơ sở của bạn đã được phê duyệt và đang hoạt động!';
                $facilityStatusType = 'success';
            } elseif ($facility->status == 'từ chối') {
                $facilityStatusMessage = 'Yêu cầu đăng ký cơ sở của bạn đã bị từ chối. Vui lòng liên hệ quản trị viên để biết thêm chi tiết.';
                $facilityStatusType = 'danger';
            }
        } else {
            $facilityStatusMessage = 'Bạn chưa đăng ký thông tin cơ sở sân. Vui lòng vào mục "Cơ Sở Của Tôi" để đăng ký.';
            $facilityStatusType = 'info';
        }

        return view('owner.index', compact('facilityStatusMessage', 'facilityStatusType', 'facility'));
    }


    public function facility()
    {
        $facility = Facility::withoutGlobalScopes()
            ->where('owner_id', Auth::id())
            ->first();
        return view('owner.facility', compact('facility'));
    }

    public function storeFacility(Request $request)
    {
        // --- VALIDATION ---
        $validatedData = $request->validate([
            'facility_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i|after:open_time',
            'description' => 'nullable|string|max:65535',
            'business_license' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'owner_phone' => 'nullable|string|max:20',
            'owner_address' => 'nullable|string|max:255',
            'owner_cccd' => ['nullable', 'string', 'max:50', Rule::unique('users', 'CCCD')->ignore(Auth::id(), 'user_id')],
        ]);

        // --- CẬP NHẬT THÔNG TIN USER ---
        $user = Auth::user();
        $user->phone = $validatedData['owner_phone'];
        $user->address = $validatedData['owner_address'];
        $user->CCCD = $validatedData['owner_cccd'];
        $user->save();

        // --- CHUẨN BỊ DỮ LIỆU FACILITY ---
        $facilityData = [
            'facility_name' => $validatedData['facility_name'],
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'open_time' => $validatedData['open_time'],
            'close_time' => $validatedData['close_time'],
            'description' => $validatedData['description'],
            'status' => 'chờ duyệt',

        ];

        // --- XỬ LÝ UPLOAD FILE GIẤY PHÉP KD (Giữ nguyên logic, nhưng đảm bảo chạy trước updateOrCreate) ---
        $relativePath = null; // Khởi tạo biến lưu đường dẫn file
        if ($request->hasFile('business_license')) {
            $file = $request->file('business_license');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $safeOriginalName = preg_replace('/[^A-Za-z0-9\-]/', '_', $originalName);
            $newFileName = time() . '_' . Str::limit($safeOriginalName, 50, '') . '.' . $extension;
            $destinationPath = public_path('img/licenses');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            try {
                $file->move($destinationPath, $newFileName);
                $relativePath = 'img/licenses/' . $newFileName; // Gán giá trị cho biến

                // Xóa file cũ chỉ khi upload file mới thành công và tìm thấy facility cũ
                $existingFacility = Facility::withoutGlobalScopes()->where('owner_id', Auth::id())->first();
                if ($existingFacility && $existingFacility->business_license_path) {
                    $oldFilePath = public_path($existingFacility->business_license_path);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi upload Giấy phép KD: ' . $e->getMessage());
                return back()->withInput()->withErrors(['business_license' => 'Không thể lưu file tải lên.']);
            }
        }

        // --- THÊM ĐƯỜNG DẪN FILE VÀO DỮ LIỆU FACILITY ---
        if ($relativePath) {
            $facilityData['business_license_path'] = $relativePath;
        }

        // --- LƯU FACILITY VÀO CSDL ---
        $facility = Facility::updateOrCreate(
            ['owner_id' => Auth::id()], // Điều kiện tìm/tạo
            $facilityData                // Dữ liệu cập nhật/tạo mới
        );
        if ($facility) { // Nếu tạo/cập nhật facility thành công
            $user = Auth::user();
            // Chỉ cập nhật nếu facility_id của user chưa đúng
            if ($user->facility_id !== $facility->facility_id) {
                $user->facility_id = $facility->facility_id; // Gán ID cơ sở vừa tạo/sửa
                $user->save(); // Lưu lại vào bảng users
            }
        } else {
            // Xử lý lỗi nếu không lưu được facility
            \Log::error('Không thể tạo/cập nhật facility cho user ID: ' . Auth::id());
            return back()->withInput()->withErrors(['general' => 'Lỗi lưu thông tin cơ sở.']);
        }
        // --- PHẢN HỒI ---
        return redirect()->route('owner.index')
            ->with('success', 'Thông tin cơ sở đã được gửi đi chờ duyệt!');
    }


    public function staff()
    {
        $owner = Auth::user();
        // --- Kiểm tra owner có facility_id không ---
        if (!$owner || !$owner->facility_id) {
            abort(403, 'Không tìm thấy thông tin cơ sở của chủ sân.');
        }
        $facilityId = $owner->facility_id;
        $staffMembers = Users::where('facility_id', $facilityId)
            ->whereIn('role_id', [3, 4])
            ->orderBy('fullname', 'asc')
            ->get();
        return view('owner.staff', compact('staffMembers'));
    }

    public function storeStaff(Request $request)
    {
        $owner = Auth::user();
        if (!$owner || !$owner->facility_id) {
            abort(403, 'Không tìm thấy thông tin cơ sở.');
        }
        $facilityId = $owner->facility_id;

        $validatedData = $request->validate([
            'fullname' => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')],
            'password' => ['required', Password::min(8)], 
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'role_id' => ['required', Rule::in([3, 4])],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            try {
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $newFileName = 'avatar_' . time() . '_' . Str::random(10) . '.' . $extension;
                $destinationPath = public_path('img/avatars');
                if (!file_exists($destinationPath))
                    mkdir($destinationPath, 0755, true);
                $file->move($destinationPath, $newFileName);
                $avatarPath = 'img/avatars/' . $newFileName;
            } catch (\Exception $e) {
                \Log::error('Lỗi upload avatar nhân viên: ' . $e->getMessage());
            }
        }

        // Tạo User dùng Model Users
        Users::create([
            'fullname' => $validatedData['fullname'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'avatar' => $avatarPath,
            'role_id' => $validatedData['role_id'],
            'facility_id' => $facilityId,
            'status' => 1, // Mặc định hoạt động
            'permissions' => $validatedData['permissions'] ?? [],
        ]);

        return redirect()->route('owner.staff')->with('success', 'Đã thêm nhân viên/quản lý mới thành công!');
    }

    /**
     * Cập nhật thông tin nhân viên/quản lý - ĐÃ BỔ SUNG LOGIC
     */
    public function updateStaff(Request $request, Users $staff) // <-- Sửa kiểu dữ liệu thành Users
    {
        $owner = Auth::user();
        // Kiểm tra quyền (Đã đúng)
        if (!$owner || !$owner->facility_id || $staff->facility_id !== $owner->facility_id || !in_array($staff->role_id, [3, 4])) {
            abort(403, 'Bạn không có quyền sửa thông tin người này.');
        }

        // --- VALIDATION (Đã đúng) ---
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:100',
            // --- SỬA LẠI: Dùng $staff->user_id ---
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($staff->user_id, 'user_id')],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()], // Bỏ confirmed nếu form ko có password_confirmation
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'status' => 'required|boolean', // 1 = active, 0 = inactive
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'role_id' => ['required', Rule::in([3, 4])],
        ]);

        // --- CHUẨN BỊ DỮ LIỆU CẬP NHẬT ---
        $updateData = [
            'fullname' => $validatedData['fullname'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'status' => $validatedData['status'],
            'permissions' => $validatedData['permissions'] ?? [], // Dùng mảng rỗng nếu không có permissions
            'role_id' => $validatedData['role_id'],
        ];

        // --- CẬP NHẬT MẬT KHẨU (NẾU CÓ) ---
        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        // --- XỬ LÝ UPLOAD AVATAR MỚI (NẾU CÓ) ---
        if ($request->hasFile('avatar')) {
            try {
                // Xóa avatar cũ trước
                if ($staff->avatar && file_exists(public_path($staff->avatar))) {
                    unlink(public_path($staff->avatar));
                }
                // Upload avatar mới
                $file = $request->file('avatar');
                $extension = $file->getClientOriginalExtension();
                $newFileName = 'avatar_' . time() . '_' . Str::random(10) . '.' . $extension;
                $destinationPath = public_path('img/avatars');
                if (!file_exists($destinationPath))
                    mkdir($destinationPath, 0755, true);
                $file->move($destinationPath, $newFileName);
                $updateData['avatar'] = 'img/avatars/' . $newFileName; // Thêm vào mảng cập nhật
            } catch (\Exception $e) {
                \Log::error('Lỗi upload avatar nhân viên (update): ' . $e->getMessage());
                // Có thể báo lỗi nếu cần
            }
        }

        // --- THỰC HIỆN CẬP NHẬT ---
        $staff->update($updateData);

        // --- PHẢN HỒI ---
        return redirect()->route('owner.staff')->with('success', 'Đã cập nhật thông tin thành công!');
    }

    /**
     * Xóa nhân viên/quản lý - ĐÃ BỔ SUNG LOGIC
     */
    public function destroyStaff(Users $staff) // <-- Sửa kiểu dữ liệu thành Users
    {
        $owner = Auth::user();
        // Kiểm tra quyền (Đã đúng)
        if (!$owner || !$owner->facility_id || $staff->facility_id !== $owner->facility_id || !in_array($staff->role_id, [3, 4])) {
            abort(403, 'Bạn không có quyền xóa người này.');
        }

        // --- XÓA AVATAR (NẾU CÓ) ---
        if ($staff->avatar && file_exists(public_path($staff->avatar))) {
            try {
                unlink(public_path($staff->avatar));
            } catch (\Exception $e) {
                \Log::error("Lỗi xóa avatar của user {$staff->user_id}: " . $e->getMessage());
            }
        }

        // --- XÓA USER ---
        $staff->delete();

        // --- PHẢN HỒI ---
        return redirect()->route('owner.staff')->with('success', 'Đã xóa nhân viên/quản lý thành công!');
    }

}