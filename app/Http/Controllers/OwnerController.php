<?php

namespace App\Http\Controllers;

// --- SỬA LẠI USE STATEMENTS ---
use App\Models\Bookings;
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


    public function facility()
    {
        $facility = Facilities::withoutGlobalScopes()
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
            'open_time' => 'required',
            'close_time' => 'required|after:open_time',
            'description' => 'nullable|string|max:65535',

            // Giấy phép kinh doanh & ảnh sân
            'business_license' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'image' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',

            // Giá
            'default_price' => 'nullable|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',

            // Thông tin chủ sở hữu
            'owner_phone' => 'nullable|string|max:20',
            'owner_address' => 'nullable|string|max:255',
            'owner_cccd' => ['nullable', 'string', 'max:50', Rule::unique('users', 'CCCD')->ignore(Auth::id(), 'user_id')],

            // 🆕 Thêm validate cho các trường mới
            'quantity_court' => 'required|integer|min:1',
            'account_no' => 'nullable|string|max:50',
            'account_bank' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // --- CẬP NHẬT THÔNG TIN USER ---
            $user = Auth::user();
            DB::table('users')->where('user_id', $user->user_id)->update([
                'phone' => $validatedData['owner_phone'],
                'address' => $validatedData['owner_address'],
                'CCCD' => $validatedData['owner_cccd'],
            ]);

            // --- CHUẨN BỊ DỮ LIỆU FACILITY ---
            $facilityData = [
                'facility_name' => $validatedData['facility_name'],
                'address' => $validatedData['address'],
                'phone' => $validatedData['phone'],
                'open_time' => $validatedData['open_time'],
                'close_time' => $validatedData['close_time'],
                'description' => $validatedData['description'],
                'status' => 'chờ duyệt',

                // 🆕 Thêm các trường mới
                'quantity_court' => $validatedData['quantity_court'],
                'account_no' => $validatedData['account_no'],
                'account_bank' => $validatedData['account_bank'],
                'account_name' => $validatedData['account_name'],
            ];

            // --- LẤY FACILITY CŨ (nếu có) ---
            $existingFacility = Facilities::withoutGlobalScopes()->where('owner_id', Auth::id())->first();

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

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi lưu thông tin cơ sở: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Lỗi lưu thông tin cơ sở. Vui lòng thử lại.']);
        }

        return redirect()->route('owner.index')->with('success', 'Thông tin cơ sở đã được gửi đi chờ duyệt!');
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

    /**
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

        // Bạn có thể lấy danh sách các sân con để điền vào bộ lọc dropdown
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