<?php

namespace App\Http\Controllers;

// --- SỬA LẠI USE STATEMENTS ---
use App\Models\Bookings;
use App\Models\Courts;
use App\Models\Invoice;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\Facilities;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use App\Models\Court_prices;
class OwnerController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        // dd($owner->user_id);
        if (!$owner) {
            abort(401, 'Unauthorized');
        }
        $facility = Facilities::withoutGlobalScopes()
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
    //=============================================================================================================
//Cở sở của tôi
    public function facility()
    {
        $facility = Facilities::withoutGlobalScopes()
            ->where('owner_id', Auth::id())
            ->first();
        return view('owner.facility', compact('facility'));
    }

    /**
     * CẬP NHẬT THÔNG TIN CƠ SỞ (không thay đổi trạng thái duyệt)
     */
    public function updateInfo(Request $request)
    {
        $facility = Facilities::withoutGlobalScopes()
            ->where('owner_id', Auth::id())
            ->first();

        // Kiểm tra điều kiện cho phép update
        if (!$facility) {
            return back()->withErrors(['general' => 'Không tìm thấy cơ sở của bạn.']);
        }

        if (!$facility->is_active || $facility->need_reapprove) {
            return back()->withErrors(['general' => 'Bạn không thể cập nhật thông tin lúc này. Vui lòng đợi admin phê duyệt.']);
        }

        // --- VALIDATION ---
        $validatedData = $request->validate([
            'facility_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'open_time' => 'required',
            'close_time' => 'required|after:open_time',
            'description' => 'nullable|string|max:65535',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
            'default_price' => 'nullable|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'quantity_court' => 'required|integer|min:1',
            'owner_phone' => 'nullable|string|max:20',
            'owner_address' => 'nullable|string|max:255',

            // Các trường nhạy cảm
            'owner_cccd' => ['nullable', 'string', 'max:50', Rule::unique('users', 'CCCD')->ignore(Auth::id(), 'user_id')],
            'account_no' => 'nullable|string|max:50',
            'account_bank' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:100',
            'business_license' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Kiểm tra các trường nhạy cảm có thay đổi không
            $sensitiveFields = [
                'owner_cccd' => Auth::user()->CCCD,
                'account_no' => $facility->account_no,
                'account_bank' => $facility->account_bank,
                'business_license' => $request->hasFile('business_license'),
            ];

            $hasSensitiveChange = false;
            foreach ($sensitiveFields as $field => $oldValue) {
                if ($field === 'business_license') {
                    if ($oldValue) {
                        $hasSensitiveChange = true;
                        break;
                    }
                } elseif (isset($validatedData[$field]) && $validatedData[$field] !== $oldValue) {
                    $hasSensitiveChange = true;
                    break;
                }
            }

            // Nếu có thay đổi nhạy cảm, chặn update và yêu cầu gửi duyệt
            if ($hasSensitiveChange) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'general' => 'Bạn đang thay đổi thông tin nhạy cảm (CCCD, Tài khoản ngân hàng, Giấy phép). Vui lòng sử dụng nút "Gửi Yêu Cầu Duyệt" để admin xét duyệt.'
                ]);
            }

            // --- CẬP NHẬT USER (chỉ phone và address) ---
            $user = Auth::user();
            DB::table('users')->where('user_id', $user->user_id)->update([
                'phone' => $validatedData['owner_phone'],
                'address' => $validatedData['owner_address'],
            ]);

            // --- LƯU SỐ LƯỢNG SÂN CŨ TRƯỚC KHI UPDATE ---
            $oldQuantity = (int) $facility->quantity_court; // Cast sang int

            Log::info('Số sân trước khi update', ['oldQuantity' => $oldQuantity]);

            // --- CHUẨN BỊ DỮ LIỆU FACILITY ---
            $facilityData = [
                'facility_name' => $validatedData['facility_name'],
                'address' => $validatedData['address'],
                'phone' => $validatedData['phone'],
                'open_time' => $validatedData['open_time'],
                'close_time' => $validatedData['close_time'],
                'description' => $validatedData['description'],
                'quantity_court' => $validatedData['quantity_court'],
            ];

            // --- UPLOAD ẢNH SÂN (không nhạy cảm) ---
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $newFileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('img/venues');

                if (!file_exists($destinationPath))
                    mkdir($destinationPath, 0755, true);
                $file->move($destinationPath, $newFileName);
                $facilityData['image'] = 'img/venues/' . $newFileName;

                // Xóa ảnh cũ
                if ($facility->image && file_exists(public_path($facility->image))) {
                    unlink(public_path($facility->image));
                }
            }

            // --- CẬP NHẬT FACILITY ---
            $facility->update($facilityData);

            // --- XỬ LÝ TẠO/XÓA SÂN KHI SỐ LƯỢNG THAY ĐỔI ---
            $newQuantity = (int) $validatedData['quantity_court']; // Cast sang int

            Log::info('So sánh số lượng sân', [
                'oldQuantity' => $oldQuantity,
                'newQuantity' => $newQuantity,
                'isDifferent' => ($oldQuantity !== $newQuantity)
            ]);

            $courtMessage = '';
            if ($oldQuantity !== $newQuantity) {
                Log::info('Số sân thay đổi, gọi autoManageCourts');

                try {
                    $courtResult = $this->autoManageCourts($facility->facility_id, $newQuantity);

                    Log::info('Courts auto-managed by owner', [
                        'facility_id' => $facility->facility_id,
                        'old_quantity' => $oldQuantity,
                        'new_quantity' => $newQuantity,
                        'action' => $courtResult['action'],
                        'courts_affected' => $courtResult['courts_affected']
                    ]);

                    if ($courtResult['message']) {
                        $courtMessage = ' ' . $courtResult['message'];
                    }
                } catch (\Exception $e) {
                    // Rollback nếu không thể xóa sân (có booking)
                    DB::rollBack();
                    Log::error('Lỗi quản lý sân: ' . $e->getMessage());
                    return back()->withInput()->withErrors(['general' => $e->getMessage()]);
                }
            } else {
                Log::info('Số sân không thay đổi, bỏ qua autoManageCourts');
            }

            // --- CẬP NHẬT GIÁ ---
            $facility->courtPrice()->updateOrCreate(
                ['facility_id' => $facility->facility_id],
                [
                    'default_price' => $validatedData['default_price'],
                    'special_price' => $validatedData['special_price'],
                ]
            );

            DB::commit();

            return redirect()->route('owner.index')->with('success', 'Cập nhật thông tin cơ sở thành công!' . $courtMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật thông tin cơ sở: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString()); // để debug
            return back()->withInput()->withErrors(['general' => 'Lỗi cập nhật thông tin cơ sở. Chi tiết: ' . $e->getMessage()]);
        }
    }

    /**
     * GỬI YÊU CẦU PHÊ DUYỆT (tạo mới hoặc cập nhật thông tin nhạy cảm)
     */
    public function requestApproval(Request $request)
    {
        // dd($request->all());
        // --- VALIDATION ---
        $validatedData = $request->validate([
            'facility_name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'open_time' => 'required',
            'close_time' => 'required|after:open_time',
            'description' => 'nullable|string|max:65535',
            'business_license' => 'nullable|file|mimetypes:image/*,application/pdf|max:2048',
            'image' => 'nullable|image|max:2048',
            'default_price' => 'nullable|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'owner_phone' => 'nullable|string|max:20',
            'owner_address' => 'nullable|string|max:255',
            'owner_cccd' => ['nullable', 'string', 'max:50', Rule::unique('users', 'CCCD')->ignore(Auth::id(), 'user_id')],
            'quantity_court' => 'required|integer|min:1',
            'account_no' => 'nullable|string|max:50',
            'account_bank' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:100',
        ]);
        // dd($validatedData);
        try {
            DB::beginTransaction();

            // --- CẬP NHẬT THÔNG TIN USER ---
            $user = Auth::user();
            DB::table('users')->where('user_id', $user->user_id)->update([
                'phone' => $validatedData['owner_phone'],
                'address' => $validatedData['owner_address'],
                'CCCD' => $validatedData['owner_cccd'],
            ]);

            // --- LẤY FACILITY CŨ (nếu có) ---
            $existingFacility = Facilities::withoutGlobalScopes()->where('owner_id', Auth::id())->first();

            // Xác định loại yêu cầu
            $isNewFacility = !$existingFacility;
            $pendingRequestType = $isNewFacility ? 'activate' : 'sensitive_update';

            // --- CHUẨN BỊ DỮ LIỆU FACILITY ---
            $facilityData = [
                'facility_name' => $validatedData['facility_name'],
                'address' => $validatedData['address'],
                'phone' => $validatedData['phone'],
                'open_time' => $validatedData['open_time'],
                'close_time' => $validatedData['close_time'],
                'description' => $validatedData['description'],
                'status' => 'chờ duyệt',
                'is_active' => 0,
                'need_reapprove' => 1,
                'pending_request_type' => $pendingRequestType,
                'quantity_court' => $validatedData['quantity_court'],
                'account_no' => $validatedData['account_no'],
                'account_bank' => $validatedData['account_bank'],
                'account_name' => $validatedData['account_name'],
            ];

            // --- UPLOAD FILE GIẤY PHÉP KINH DOANH ---
            if ($request->hasFile('business_license')) {
                $file = $request->file('business_license');
                $newFileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('img/licenses');

                if (!file_exists($destinationPath))
                    mkdir($destinationPath, 0755, true);
                $file->move($destinationPath, $newFileName);
                $facilityData['business_license'] = 'img/licenses/' . $newFileName;

                // Xóa file cũ nếu có
                if ($existingFacility && $existingFacility->business_license && file_exists(public_path($existingFacility->business_license))) {
                    unlink(public_path($existingFacility->business_license));
                }
            }

            // --- UPLOAD ẢNH SÂN ---
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $newFileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('img/venues');

                if (!file_exists($destinationPath))
                    mkdir($destinationPath, 0755, true);
                $file->move($destinationPath, $newFileName);
                $facilityData['image'] = 'img/venues/' . $newFileName;

                // Xóa ảnh cũ
                if ($existingFacility && $existingFacility->image && file_exists(public_path($existingFacility->image))) {
                    unlink(public_path($existingFacility->image));
                }
            }

            // --- LƯU HOẶC CẬP NHẬT FACILITY ---
            $facility = Facilities::updateOrCreate(
                ['owner_id' => Auth::id()],
                $facilityData
            );

            // --- LƯU GIÁ ---
            if ($facility) {
                $facility->courtPrice()->updateOrCreate(
                    ['facility_id' => $facility->facility_id],
                    [
                        'default_price' => $validatedData['default_price'],
                        'special_price' => $validatedData['special_price'],
                    ]
                );

                if ($user->facility_id !== $facility->facility_id) {
                    DB::table('users')->where('user_id', $user->user_id)->update([
                        'facility_id' => $facility->facility_id,
                    ]);
                }
            } else {
                throw new \Exception('Không thể tạo hoặc cập nhật facility.');
            }

            DB::commit();

            $message = $isNewFacility
                ? 'Yêu cầu đăng ký cơ sở đã được gửi đi chờ duyệt!'
                : 'Yêu cầu cập nhật thông tin nhạy cảm đã được gửi đi chờ duyệt!';

            return redirect()->route('owner.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi gửi yêu cầu duyệt cơ sở: ' . $e->getMessage());

            // Trả về thông báo lỗi chi tiết cho người dùng
            return back()->withInput()->withErrors([
                'general' => 'Lỗi gửi yêu cầu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * TỰ ĐỘNG TẠO/XÓA SÂN KHI OWNER THAY ĐỔI SỐ LƯỢNG
     */
    private function autoManageCourts($facilityId, $quantity)
    {
        Log::info('=== BẮT ĐẦU autoManageCourts (Logic Composite Key) ===', [
            'facility_id' => $facilityId,
            'quantity_requested' => $quantity
        ]);

        // Đếm số sân hiện có
        $existingCount = Courts::where('facility_id', $facilityId)->count();
        Log::info('Số sân hiện có trong DB: ' . $existingCount);

        // TRƯỜNG HỢP 1: TẠO THÊM SÂN
        if ($existingCount < $quantity) {
            $courtsToAdd = $quantity - $existingCount;

            // 1. Lấy ID lớn nhất hiện tại TRONG PHẠM VI CƠ SỞ ĐÓ
            // Nếu chưa có sân nào thì trả về 0
            $currentMaxId = Courts::where('facility_id', $facilityId)->max('court_id') ?? 0;
            
            $nextId = $currentMaxId; 

            for ($i = 1; $i <= $courtsToAdd; $i++) {
                $nextId++; // Tăng ID lên 

                // Tính tên sân hiển thị (Dựa trên tổng số lượng)
                $courtNameNumber = $existingCount + $i;

                try {
                    Courts::create([
                        'court_id'    => $nextId, // Lưu số thứ tự (1, 2, 3...)
                        'facility_id' => $facilityId,
                        'court_name'  => "Sân {$courtNameNumber}",
                        'status'      => 'Trống',
                    ]);

                    Log::info("✓ Đã tạo Sân {$courtNameNumber} (court_id: {$nextId}) cho facility {$facilityId}");
                } catch (\Exception $e) {
                    Log::error('✗ Lỗi tạo sân: ' . $e->getMessage());
                    throw $e;
                }
            }

            return [
                'action' => 'added',
                'courts_affected' => $courtsToAdd,
                'message' => "Đã tạo thêm {$courtsToAdd} sân mới."
            ];
        }

        // TRƯỜNG HỢP 2: XÓA BỚT SÂN
        if ($existingCount > $quantity) {
            // Lấy các sân có court_id lớn nhất CỦA CƠ SỞ NÀY để xóa
            $courtsToRemove = Courts::where('facility_id', $facilityId)
                ->orderBy('court_id', 'desc')
                ->take($existingCount - $quantity)
                ->get();

            $hasActiveBookings = false;
            $bookingsInfo = [];

            // Kiểm tra booking (Phải check cả facility_id và court_id)
            foreach ($courtsToRemove as $court) {
                $bookingCount = DB::table('bookings')
                    ->where('facility_id', $facilityId) 
                    ->where('court_id', $court->court_id)
                    ->whereIn('status', ['confirmed', 'pending'])
                    ->where('booking_date', '>=', now()->toDateString())
                    ->count();

                if ($bookingCount > 0) {
                    $hasActiveBookings = true;
                    $bookingsInfo[] = $court->court_name;
                }
            }

            if ($hasActiveBookings) {
                $courtNames = implode(', ', $bookingsInfo);
                throw new \Exception(
                    "Không thể giảm số lượng sân vì các sân sau đang có lịch đặt: {$courtNames}. " .
                    "Vui lòng hủy lịch hoặc chờ khách đá xong."
                );
            }

            $courtsRemovedCount = $existingCount - $quantity;

            // Thực hiện xóa
            foreach ($courtsToRemove as $court) {
                // xóa chính xác theo cặp (facility_id + court_id) để tránh xóa nhầm sân của cơ sở khác
                Courts::where('facility_id', $facilityId)
                      ->where('court_id', $court->court_id)
                      ->delete();
            }

            Log::info("Đã xóa {$courtsRemovedCount} sân của facility {$facilityId}");

            return [
                'action' => 'removed',
                'courts_affected' => $courtsRemovedCount,
                'message' => "Đã xóa {$courtsRemovedCount} sân."
            ];
        }

        // Trường hợp 3: Không thay đổi
        return [
            'action' => 'unchanged',
            'courts_affected' => 0,
            'message' => ''
        ];
    }
    //=============================================================================================================
//Nhân viên
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
                Log::error('Lỗi upload avatar nhân viên: ' . $e->getMessage());
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
     * Cập nhật thông tin nhân viên/quản lý 
     */
    public function updateStaff(Request $request, Users $staff)
    {
        $owner = Auth::user();
        // Kiểm tra quyền
        if (!$owner || !$owner->facility_id || $staff->facility_id !== $owner->facility_id || !in_array($staff->role_id, [3, 4])) {
            abort(403, 'Bạn không có quyền sửa thông tin người này.');
        }

        // --- VALIDATION ---
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:100',
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

        // --- CẬP NHẬT MẬT KHẨU ---
        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        // --- XỬ LÝ UPLOAD AVATAR MỚI ---
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
                Log::error('Lỗi upload avatar nhân viên (update): ' . $e->getMessage());
                // Có thể báo lỗi nếu cần
            }
        }

        // --- THỰC HIỆN CẬP NHẬT ---
        $staff->update($updateData);

        // --- PHẢN HỒI ---
        return redirect()->route('owner.staff')->with('success', 'Đã cập nhật thông tin thành công!');
    }

    /**
     * Xóa nhân viên/quản lý
     */
    public function destroyStaff(Users $staff)
    {
        $owner = Auth::user();
        // Kiểm tra quyền
        if (!$owner || !$owner->facility_id || $staff->facility_id !== $owner->facility_id || !in_array($staff->role_id, [3, 4])) {
            abort(403, 'Bạn không có quyền xóa người này.');
        }

        // --- XÓA AVATAR ---
        if ($staff->avatar && file_exists(public_path($staff->avatar))) {
            try {
                unlink(public_path($staff->avatar));
            } catch (\Exception $e) {
                Log::error("Lỗi xóa avatar của user {$staff->user_id}: " . $e->getMessage());
            }
        }

        // --- XÓA USER ---
        $staff->delete();

        // --- PHẢN HỒI ---
        return redirect()->route('owner.staff')->with('success', 'Đã xóa nhân viên/quản lý thành công!');
    }
    //=============================================================================================================
    /**
     * API: Lấy danh sách sân con
     */
    public function getCourts(Request $request)
    {
        try {
            $owner = Auth::user();

            if (!$owner || !$owner->facility_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            $courts = Courts::where('facility_id', $owner->facility_id)
                ->orderBy('court_id', 'asc')
                ->get(['court_id', 'court_name', 'status']);

            Log::info('Courts loaded for owner', [
                'facility_id' => $owner->facility_id,
                'court_count' => $courts->count()
            ]);

            return response()->json([
                'success' => true,
                'courts' => $courts,
                'total' => $courts->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading courts', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Server error'
            ], 500);
        }
    }    /**
         * Hiển thị trang Báo cáo Doanh thu & Sân
         */
    public function reports()
    {
        // Lấy facility_id của owner để truyền sang view (dùng cho JS sau này)
        $owner = Auth::user();
        if (!$owner || !$owner->facility_id) {
            abort(403, 'Không tìm thấy thông tin cơ sở.');
        }
        $facilityId = $owner->facility_id;

        // Lấy danh sách các sân con để điền vào bộ lọc dropdown
        $courts = \App\Models\Courts::where('facility_id', $facilityId)->get(['court_id', 'court_name']);

        return view('owner.reports', compact('facilityId', 'courts'));
    }

    /**
     * API: Cung cấp dữ liệu báo cáo cho Chart.js (AJAX)
     */
    public function getReportData(Request $request)
    {
        // --- 1. KIỂM TRA QUYỀN VÀ LẤY FACILITY ID ---
        $owner = Auth::user();
        if (!$owner || !$owner->facility_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $facilityId = $owner->facility_id;

        // --- 2. LẤY BỘ LỌC TỪ REQUEST ---
        $startDate = Carbon::parse($request->get('start_date'))->startOfDay();
        $endDate = Carbon::parse($request->get('end_date'))->endOfDay();
        $courtFilter = $request->get('court_id'); // Lọc theo sân con

        // BÁO CÁO 1: BIỂU ĐỒ DOANH THU THEO NGÀY
        // ===============================================
        $revenueByDay = Invoice::where('payment_status', 'Đã thanh toán')
            ->where('issue_date', '>=', $startDate->toDateString())
            ->where('issue_date', '<=', $endDate->toDateString())
            ->whereHas('invoiceDetails.booking', function ($query) use ($facilityId) {
                // Đảm bảo hóa đơn chỉ liên quan đến cơ sở này
                $query->where('facility_id', $facilityId);
            })
            ->select(
                DB::raw('DATE_FORMAT(issue_date, "%d/%m") as label'),
                DB::raw('SUM(final_amount) as total')
            )
            ->groupBy('issue_date') // Group theo ngày
            ->orderBy('issue_date', 'asc')
            ->get();


        // BÁO CÁO 2: TỈ LỆ SỬ DỤNG SÂN (THEO SÂN CON)
        // ===============================================
        $utilizationQuery = Bookings::where('facility_id', $facilityId)
            ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($courtFilter) {
            $utilizationQuery->where('court_id', $courtFilter);
        }

        $utilizationByCourt = $utilizationQuery
            ->join('courts', 'bookings.court_id', '=', 'courts.court_id')
            ->select(
                'courts.court_name as label',
                DB::raw('COUNT(bookings.booking_id) as count')
            )
            ->groupBy('courts.court_name')
            ->get();

        // 3. TRẢ VỀ DỮ LIỆU JSON
        // ===============================================
        return response()->json([
            'revenue_data' => [
                'labels' => $revenueByDay->pluck('label'),
                'data' => $revenueByDay->pluck('total'),
            ],
            'utilization_data' => [
                'labels' => $utilizationByCourt->pluck('label'),
                'data' => $utilizationByCourt->pluck('count'),
            ],
        ]);
    }
}