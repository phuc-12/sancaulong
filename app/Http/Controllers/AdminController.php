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

class AdminController extends Controller
{
    // === Hiển thị thông tin ===
    public function index()
    {
        $STATUS_PAID = 'Đã thanh toán';
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        // === TÍNH TOÁN THEO NGÀY ===
        // --- 1. Lấy số khách hàng mới (Hôm nay) ---
        $today = Carbon::today(); // Lấy ngày hôm nay
        $newCustomersToday = Users::withoutGlobalScopes()
            ->where('role_id', 5)
            ->whereDate('created_at', $today) // So sánh ngày
            ->count();
        // --- 2. Lấy số khách hàng mới (Hôm qua) ---
        $yesterday = Carbon::yesterday(); // Lấy ngày hôm qua
        $newCustomersYesterday = Users::withoutGlobalScopes()
            ->where('role_id', 5)
            ->whereDate('created_at', $yesterday) // So sánh ngày
            ->count();
        // --- 3. Tính toán % thay đổi ---
        $percentageChange = 0;
        $growthStatus = 'neutral';

        if ($newCustomersYesterday > 0) {
            // Tính % chênh lệch
            $percentageChange = (($newCustomersToday - $newCustomersYesterday) / $newCustomersYesterday) * 100;
        } elseif ($newCustomersToday > 0) {
            // Hôm qua 0, hôm nay > 0 => Tăng 100%
            $percentageChange = 100;
        }

        $percentageChange = round($percentageChange, 1);
        if ($percentageChange > 0) {
            $growthStatus = 'up';
        } elseif ($percentageChange < 0) {
            $growthStatus = 'down';
        }
        $percentageChangeAbs = abs($percentageChange);
        //=============================================================================================================
//CART

        // === TÍNH TOÁN DOANH THU ===
        // --- 1. Lấy doanh thu (Tháng này) ---
        $now = Carbon::now();
        $revenueThisMonth = Invoice::where('payment_status', $STATUS_PAID)
            ->whereYear('issue_date', $now->year)
            ->whereMonth('issue_date', $now->month)
            ->sum('final_amount');
        // --- 2. Lấy doanh thu (Tháng trước) ---
        $lastMonth = Carbon::now()->subMonth();
        $revenueLastMonth = Invoice::where('payment_status', $STATUS_PAID)
            ->whereYear('issue_date', $lastMonth->year)
            ->whereMonth('issue_date', $lastMonth->month)
            ->sum('final_amount');
        // --- 3. Tính toán % thay đổi ---
        $revenuePercentageChange = 0;
        $revenueGrowthStatus = 'neutral';
        if ($revenueLastMonth > 0) {
            $revenuePercentageChange = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
        } elseif ($revenueThisMonth > 0) {
            $revenuePercentageChange = 100;
        }
        $revenuePercentageChange = round($revenuePercentageChange, 1);
        if ($revenuePercentageChange > 0)
            $revenueGrowthStatus = 'up';
        elseif ($revenuePercentageChange < 0)
            $revenueGrowthStatus = 'down';
        $revenuePercentageChangeAbs = abs($revenuePercentageChange);

        // === ĐẾM SỐ DOANH NGHIỆP ===
        $totalOwners = Users::withoutGlobalScopes()
            ->where('role_id', 2) // 2 = Doanh nghiệp
            ->count();
        // === ĐẾM SỐ ĐẶT SÂN HÔM NAY ===
        $totalBookingsToday = Bookings::whereDate('created_at', $today)
            ->count();

        // === LẤY DANH SÁCH CƠ SỞ CHỜ DUYỆT ===
        $pendingFacilities = Facilities::where('status', 'chờ duyệt')
            ->with('owner') // Lấy kèm thông tin chủ sân (owner)
            ->orderBy('created_at', 'asc') // Ưu tiên cái cũ (nếu có timestamps)
            ->get();

        // --- 4. Trả dữ liệu về View ---
        return view('admin.index', [
            // Dữ liệu khách hàng
            'newCustomersCount' => $newCustomersToday,
            'customerPercentageChange' => $percentageChangeAbs,
            'customerGrowthStatus' => $growthStatus,

            // Dữ liệu doanh thu
            'totalRevenueThisMonth' => $revenueThisMonth,
            'revenuePercentageChange' => $revenuePercentageChangeAbs,
            'revenueGrowthStatus' => $revenueGrowthStatus,

            // Dữ liệu doanh nghiệp
            'totalOwners' => $totalOwners,

            // Dữ liệu đặt sân
            'totalBookingsToday' => $totalBookingsToday,

            // Danh sách cơ sở chờ duyệt
            'pendingFacilities' => $pendingFacilities,
        ]);
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

    // === XỬ LÝ PHÊ DUYỆT 1 CƠ SỞ ===
    /**
     * Duyệt facility và tự động tạo court_prices
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

            // Cập nhật trạng thái facility
            $facility->status = 'đã duyệt';
            $facility->save();

            // Tự động tạo các bản ghi court_prices dựa trên giá đã nhập
            $this->createCourtPrices($facility);

            DB::commit();

            return redirect()->route('admin.facilities.approval')
                ->with('success', 'Đã duyệt cơ sở thành công và tạo bảng giá!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi duyệt facility: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi duyệt cơ sở.']);
        }
    }

    /**
     * Tạo các bản ghi court_prices từ thông tin giá của facility
     */
    private function createCourtPrices(Facilities $facility)
    {
        $today = now()->toDateString();
        // Tạo 1 bản ghi duy nhất với cả 2 giá
        Court_prices::create([
            'facility_id' => $facility->facility_id,
            'default_price' => $facility->default_price,   // Giờ mặc định (05:00-16:00)
            'special_price' => $facility->special_price,   // Giờ cao điểm (16:00-23:00)
            'effective_date' => $today,
        ]);
    }

    /**
     * Từ chối facility
     */
    public function reject(Request $request, $facilityId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $facility = Facilities::findOrFail($facilityId);

        $facility->status = 'từ chối';
        $facility->rejection_reason = $request->rejection_reason;
        $facility->save();

        return redirect()->route('admin.facilities.approval')
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
        // (Kiểm tra quyền Admin)
        $facility->update(['status' => 'tạm khóa']); // Đặt trạng thái là 'tạm khóa'
        return redirect()->route('admin.facilities.index')
            ->with('success', "Đã tạm khóa hoạt động của cơ sở '{$facility->facility_name}'.");
    }

    //KÍCH HOẠT LẠI CƠ SỞ
    public function activateFacility(Facilities $facility)
    {
        // (Kiểm tra quyền Admin)
        // Kích hoạt lại đồng nghĩa với việc duyệt lại
        $facility->update(['status' => 'đã duyệt']); // Đặt trạng thái là 'đã duyệt'
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
            'password' => ['nullable', 'confirmed', Password::min(8)],
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
