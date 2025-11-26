<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Court_prices;
use App\Models\Facilities;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Invoice;
use App\Models\Facility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use App\Models\Courts;
use Illuminate\Support\Facades\Log;
class AdminController extends Controller
{
    // === Hiển thị thông tin ===
    public function index()
    {

        // 1. Tổng số chủ sân (Facilities)
        $totalOwners = Facilities::count();

        // 2. Tổng số sân con
        $totalCourts = Courts::count();

        // 3. Tổng số người dùng
        $totalUsers = Users::count();

        // 4. Tổng doanh thu toàn hệ thống
        $totalSystemRevenue = Bookings::sum('unit_price');

        // 5. Tăng trưởng chủ sân theo tháng
        $ownersThisMonth = Facilities::whereMonth('created_at', now()->month)->count();
        $ownersLastMonth = Facilities::whereMonth('created_at', now()->subMonth()->month)->count();

        $ownerGrowth = $ownersLastMonth == 0 ? 100 :
            (($ownersThisMonth - $ownersLastMonth) / $ownersLastMonth) * 100;

        $ownerGrowthStatus = $ownersThisMonth > $ownersLastMonth ? 'up' :
            ($ownersThisMonth < $ownersLastMonth ? 'down' : 'neutral');

        // 6. Tăng trưởng người dùng theo tháng
        $usersThisMonth = Users::whereMonth('created_at', now()->month)->count();
        $usersLastMonth = Users::whereMonth('created_at', now()->subMonth()->month)->count();

        $userGrowth = $usersLastMonth == 0 ? 100 :
            (($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100;

        $userGrowthStatus = $usersThisMonth > $usersLastMonth ? 'up' :
            ($usersThisMonth < $usersLastMonth ? 'down' : 'neutral');

        // 7. Số yêu cầu đang cần phê duyệt (cho alert)
        $pendingRequestsCount = Facilities::withoutGlobalScopes()
            ->where('need_reapprove', 1)
            ->count();


        //8. DỮ LIỆU BIỂU ĐỒ TĂNG TRƯỞNG 12 THÁNG (Line Chart)
        $ownersMonthly = Facilities::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $usersMonthly = Users::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');


        return view('admin.index', compact(
            'totalOwners',
            'totalCourts',
            'totalUsers',
            'totalSystemRevenue',
            'ownerGrowth',
            'ownerGrowthStatus',
            'userGrowth',
            'userGrowthStatus',
            'ownersMonthly',
            'usersMonthly',
            'pendingRequestsCount'
        ));
    }

    public function pendingFacilities()
    {
        $pendingFacilities = Facilities::withoutGlobalScopes()
            ->where('need_reapprove', 1)
            ->get();

        return view('admin.facilities.pending', compact('pendingFacilities'));
    }
    //=============================================================================================================
//BIỂU ĐỒ

    // === CUNG CẤP DỮ LIỆU CHO BIỂU ĐỒ ===
    public function getRevenueData(Request $request)
    {
        $STATUS_PAID = 'Đã thanh toán';
        $filter = $request->get('filter', '30days');

        $query = Invoice::where('payment_status', $STATUS_PAID);

        // 1. Lọc theo thời gian VÀ định nghĩa chuỗi groupBy
        $groupBySql = ""; // Sẽ lưu chuỗi SQL
        if ($filter == 'this_week') {
            $query->whereBetween('issue_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $groupBySql = "DATE_FORMAT(issue_date, 'Ngày %e/%c')";

        } elseif ($filter == 'this_month') {
            $query->whereYear('issue_date', Carbon::now()->year)
                ->whereMonth('issue_date', Carbon::now()->month);
            $groupBySql = "DATE_FORMAT(issue_date, 'Ngày %e/%c')";

        } elseif ($filter == 'this_year') {
            $query->whereYear('issue_date', Carbon::now()->year);
            $groupBySql = "DATE_FORMAT(issue_date, 'Tháng %c/%Y')";

        } else { // Mặc định: '30days'
            $query->whereBetween('issue_date', [Carbon::now()->subDays(30), Carbon::now()]);
            $groupBySql = "DATE_FORMAT(issue_date, 'Ngày %e/%c')";
        }

        // 2. Truy vấn CSDL
        $revenueData = $query
            ->select(
                // FIX 1: Dùng DB::raw() với chuỗi SQL đã xây dựng
                DB::raw("$groupBySql as label"),
                DB::raw('SUM(final_amount) as total'),
                DB::raw('MIN(issue_date) as sort_date')
            )
            // FIX 2: Group by bằng chính chuỗi SQL (DB::raw)
            ->groupBy(DB::raw($groupBySql))
            ->orderBy('sort_date', 'ASC') // Luôn sắp xếp theo ngày
            ->get();

        // 3. Tách labels và data
        $labels = $revenueData->pluck('label');
        $data = $revenueData->pluck('total');

        // 4. Trả về JSON
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    //=============================================================================================================
//DOANH NGHIỆP
    /**
     * Duyệt facility
     */
    public function approve(Request $request, $facilityId)
    {
        DB::beginTransaction();

        try {
            $facility = Facilities::findOrFail($facilityId);

            // Kiểm tra trạng thái hiện tại
            if ($facility->status !== 'chờ duyệt') {
                return back()->withErrors(['error' => 'Cơ sở này không ở trạng thái chờ duyệt.']);
            }

            // 1. Cập nhật trạng thái facility
            $facility->status = 'đã duyệt';
            $facility->is_active = 1;
            $facility->need_reapprove = 0;

            $requestType = $facility->pending_request_type;
            $facility->pending_request_type = null;

            $facility->save();

            $successMessage = '';

            // 2. Xử lý theo loại yêu cầu
            if ($requestType === 'activate') {
                // Yêu cầu kích hoạt lần đầu

                // Tự động tạo bảng giá
                $this->createCourtPrices($facility);

                // Tự động tạo sân con
                $quantityCourt = $facility->quantity_court ?? 0;
                $successMessage = "Đã duyệt và kích hoạt cơ sở '{$facility->facility_name}' thành công!";

                if ($quantityCourt > 0) {
                    $courtResult = $this->autoCreateCourts($facility->facility_id, $quantityCourt);

                    Log::info('Các sân con đã được tạo cho cơ sở mới', [
                        'facility_id' => $facility->facility_id,
                        'facility_name' => $facility->facility_name,
                        'requested_quantity' => $quantityCourt,
                        'action' => $courtResult['action'],
                        'courts_affected' => $courtResult['courts_affected']
                    ]);

                    $successMessage .= ' ' . $courtResult['message'];
                }
            } elseif ($requestType === 'sensitive_update') {
                // Yêu cầu cập nhật thông tin nhạy cảm
                // ❌ KHÔNG tạo/xóa sân ở đây nữa - owner tự quản lý

                $successMessage = "Đã duyệt cập nhật thông tin nhạy cảm cho cơ sở '{$facility->facility_name}'!";

                Log::info('Cập nhật nhạy cảm đã được phê duyệt', [
                    'facility_id' => $facility->facility_id,
                    'facility_name' => $facility->facility_name,
                ]);
            } else {
                $successMessage = "Đã duyệt cơ sở '{$facility->facility_name}' thành công!";
            }

            DB::commit();

            return redirect()->route('admin.facilities.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Lỗi khi phê duyệt cơ sở', [
                'facility_id' => $facilityId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Tự động tạo sân con khi duyệt cơ sở lần đầu
     */
    private function autoCreateCourts($facilityId, $quantity)
    {
        $existingCount = Courts::where('facility_id', $facilityId)->count();

        // Chỉ tạo sân nếu chưa có hoặc thiếu
        if ($existingCount < $quantity) {
            $courts = [];
            $courtsToAdd = $quantity - $existingCount;

            for ($i = $existingCount + 1; $i <= $quantity; $i++) {
                $courts[] = [
                    'court_id' => $i,
                    'facility_id' => $facilityId,
                    'court_name' => "Sân {$i}",
                    'status' => 'Trống',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Courts::insert($courts);

            Log::info('Các sân con được tạo khi cơ sở được phê duyệt', [
                'facility_id' => $facilityId,
                'existing_count' => $existingCount,
                'courts_added' => count($courts),
                'new_total' => $quantity
            ]);

            return [
                'action' => 'added',
                'courts_affected' => count($courts),
                'message' => "Đã tạo {$courtsToAdd} sân (Sân " . ($existingCount + 1) . " đến Sân {$quantity})."
            ];
        }

        // Trường hợp đã có đủ sân (không làm gì)
        Log::info('Các sân con đã tồn tại', [
            'facility_id' => $facilityId,
            'quantity' => $quantity
        ]);

        return [
            'action' => 'unchanged',
            'courts_affected' => 0,
            'message' => "Cơ sở đã có {$quantity} sân."
        ];
    }

    /**
     * Tạo các bản ghi court_prices từ thông tin giá của facility
     */
    private function createCourtPrices(Facilities $facility)
    {
        $today = now()->toDateString();

        Court_prices::updateOrCreate(
            ['facility_id' => $facility->facility_id],
            [
                'default_price' => $facility->courtPrice->default_price ?? 0,
                'special_price' => $facility->courtPrice->special_price ?? 0,
                'effective_date' => $today,
            ]
        );
    }

    /**
     * Từ chối facility
     */
    public function reject(Request $request, $facilityId)
    {
        $facility = Facilities::findOrFail($facilityId);

        $facility->status = 'từ chối';
        $facility->is_active = 0;
        $facility->need_reapprove = 1;
        // Giữ nguyên pending_request_type để owner biết yêu cầu nào bị từ chối
        $facility->save();

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Đã từ chối cơ sở.');
    }

    //Hiển thị trang Quản lý Cơ sở (Doanh nghiệp)
    public function manageFacilities()
    {
        $facilities = Facilities::with('owner')
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.facilities.index', compact('facilities'));
    }

    //TẠM KHÓA CƠ SỞ
    public function suspendFacility(Facilities $facility)
    {
        $facility->update([
            'status' => 'tạm khóa',
            'is_active' => 0
        ]);

        return redirect()->route('admin.facilities.index')
            ->with('success', "Đã tạm khóa hoạt động của cơ sở '{$facility->facility_name}'.");
    }

    //KÍCH HOẠT LẠI CƠ SỞ
    public function activateFacility(Facilities $facility)
    {
        $facility->update([
            'status' => 'đã duyệt',
            'is_active' => 1,
            'need_reapprove' => 0,
            'pending_request_type' => null
        ]);

        return redirect()->route('admin.facilities.index')
            ->with('success', "Đã kích hoạt lại hoạt động cho cơ sở '{$facility->facility_name}'.");
    }
    //=============================================================================================================
//KHÁCH HÀNG

    // === HIỂN THỊ DANH SÁCH KHÁCH HÀNG ===
    public function listCustomers()
    {
        // Lấy tất cả user có role_id = 5 (Customer)
        $customers = Users::where('role_id', 5)
            ->orderBy('fullname', 'asc')
            ->paginate(15); // Phân trang

        // Trả về view mới, truyền danh sách khách hàng
        return view('admin.customers.index', compact('customers'));
    }

    // ===  HIỂN THỊ FORM SỬA KHÁCH HÀNG ===
    public function editCustomer(Users $user) // Laravel tự động tìm User (Users)
    {
        // Kiểm tra lại để chắc chắn đây là khách hàng
        if ($user->role_id != 5) {
            abort(404, 'Không phải tài khoản khách hàng.');
        }

        // Trả về view form sửa, truyền thông tin $user
        return view('admin.customers.edit', compact('user'));
    }

    // ===  XỬ LÝ CẬP NHẬT KHÁCH HÀNG ===
    public function updateCustomer(Request $request, Users $user)
    {
        // Kiểm tra lại
        if ($user->role_id != 5) {
            abort(404);
        }

        // --- VALIDATION DỮ LIỆU ---
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'CCCD' => ['nullable', 'string', 'max:50', Rule::unique('users', 'CCCD')->ignore($user->user_id, 'user_id')],
            // Email phải duy nhất, ngoại trừ chính user này
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'status' => 'required|boolean',
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        // --- CHUẨN BỊ DỮ LIỆU CẬP NHẬT ---
        $updateData = [
            'fullname' => $validatedData['fullname'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'CCCD' => $validatedData['CCCD'],
            'status' => $validatedData['status'],
        ];

        // --- CẬP NHẬT MẬT KHẨU (NẾU ADMIN NHẬP) ---
        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        // --- CẬP NHẬT VÀO CSDL ---
        $user->update($updateData);

        // --- PHẢN HỒI ---
        // Quay lại trang danh sách khách hàng
        return redirect()->route('admin.customers.index')
            ->with('success', "Đã cập nhật thông tin khách hàng '{$user->fullname}' thành công!");
    }
}
