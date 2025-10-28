<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Invoice;
use App\Models\Facility;

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
        $pendingFacilities = Facility::where('status', 'chờ duyệt')
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

    // === XỬ LÝ PHÊ DUYỆT 1 CƠ SỞ===
    public function approveFacility(Facility $facility) 
    {
        // Cập nhật trạng thái thành 'đã duyệt'
        $facility->update(['status' => 'đã duyệt']); 
        
        // (Tùy chọn: Gửi email thông báo cho chủ sân)

        // Quay lại trang admin với thông báo thành công
        return redirect()->route('admin.index')->with('success', "Đã phê duyệt cơ sở '{$facility->facility_name}'.");
    }

    // === XỬ LÝ TỪ CHỐI 1 CƠ SỞ ===
    public function denyFacility(Facility $facility) 
    {
        // Cập nhật trạng thái thành 'từ chối'
        $facility->update(['status' => 'từ chối']); 
        
        // (Tùy chọn: Gửi email thông báo cho chủ sân)
        
        // Quay lại trang admin với thông báo thành công
        return redirect()->route('admin.index')->with('success', "Đã từ chối cơ sở '{$facility->facility_name}'.");
    }

}
