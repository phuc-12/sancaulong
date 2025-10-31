<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\Request;
use App\Models\facilities;
use App\Models\Users;
use App\Models\Time_slots;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
class HomeController extends Controller
{
    const LIMIT_PER_LOAD = 10;
    public function index()
    {
        $sancaulong = Facilities::with('Court_prices')->take(3)->get();
        // dd($sancaulong->toArray());
        return view('index', compact('sancaulong'));
    }

    public function listing_grid()
    {
        $limit = self::LIMIT_PER_LOAD;

        // 1. Lấy tổng số sân đang hoạt động (cho mục đích hiển thị tổng số)
        $total_count = Facilities::where('status', '1')->count();

        // 2. Lấy danh sách sân lần đầu tiên (Chỉ 10 sân)
        $danhsachsan = Facilities::query()
            ->where('status', '1')
            ->take($limit)
            ->get();

        // 3. Kiểm tra trạng thái còn dữ liệu để tải nữa hay không
        // Nếu số lượng sân lấy được bằng LIMIT, thì chắc chắn còn dữ liệu tiếp theo.
        $hasMoreData = $danhsachsan->count() === $limit;

        // 4. Truyền các biến cần thiết sang Blade
        return view('listing-grid', compact('danhsachsan', 'hasMoreData', 'total_count', 'limit'));
    }

    // HÀM MỚI: Xử lý request AJAX/Fetch (Trả về JSON)
    public function load_more_san(Request $request)
    {
        $limit = self::LIMIT_PER_LOAD;
        $offset = $request->input('offset', 0); // Lấy offset từ JavaScript

        // Query cơ sở dữ liệu với skip và take
        $sans = Facilities::query()
            ->where('status', '1') // Dùng cùng trạng thái với hàm trên
            ->skip($offset)
            ->take($limit)
            ->get();

        // Kiểm tra còn dữ liệu để tải nữa hay không
        $hasMore = $sans->count() === $limit;

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'data' => $sans,
            'offset' => (int) $offset + $limit, // Cập nhật offset mới cho JS
            'hasMore' => $hasMore,
        ]);
    }

    public function venue_details(Request $request)
    {
        $idSan = $request->query('idSan');

        $thongtinsan = Facilities::with('Users')->get()->where('facility_id', $idSan)->first();

        if (!$thongtinsan) {
            return response()->json(['error' => 'Không tìm thấy sản phẩm'], 404);
        }

        return view('venue-details', compact('thongtinsan'));
    }

    public function show($idSan)
    {
        // Lấy thông tin sân
        $thongtinsan = Facilities::where('facility_id', $idSan)->firstOrFail();

        // Lấy thông tin khách hàng (nếu đã đăng nhập)
        $customer = Auth::check() ? Users::where('user_id', Auth::id())->first() : null;

        // Lấy danh sách khung giờ
        $timeSlots = Time_slots::all();

        // Lấy danh sách ngày (7 ngày tiếp theo, ví dụ)
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $dates[] = now()->addDays($i)->format('Y-m-d');
        }

        // Lấy danh sách đặt sân từ DB
        $bookings = Bookings::where('facility_id', $idSan)
            ->whereIn('booking_date', $dates)
            ->get(['booking_date', 'time_slot_id', 'court_id']);

        $bookingsData = [];
        foreach ($bookings as $b) {
            $bookingsData[$b->booking_date][$b->time_slot_id][$b->court_id] = true;
        }


        // Từ điển chuyển đổi thứ sang tiếng Việt
        $thuTiengViet = [
            'Mon' => 'Thứ hai',
            'Tue' => 'Thứ ba',
            'Wed' => 'Thứ tư',
            'Thu' => 'Thứ năm',
            'Fri' => 'Thứ sáu',
            'Sat' => 'Thứ bảy',
            'Sun' => 'Chủ nhật',
        ];

        // Số sân con
        $soLuongSan = $thongtinsan->quantity_court;

        // Tạo danh sách sân con như: San 1, San 2, San 3...
        $dsSanCon = [];
        for ($i = 1; $i <= $soLuongSan; $i++) {
            $dsSanCon[] = [
                'id' => $thongtinsan->facility_id . '-' . $i,   // Ví dụ SAN001-1
                'ten' => 'Sân ' . $i
            ];
        }
        // dd($customer->toArray());
        return view('venue-details', compact('thongtinsan', 'customer', 'timeSlots', 'dates', 'bookingsData', 'thuTiengViet', 'soLuongSan', 'dsSanCon'));
    }

    public function addSlot(Request $request)
    {
        $slots = session('selected_slots', []);

        $slotInfo = [
            'court' => $request->court,
            'date' => $request->date,
            'slot' => $request->slot,
            'price' => $request->price,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ];

        // Toggle: nếu đã tồn tại thì xóa
        $existsKey = null;
        foreach ($slots as $key => $s) {
            if ($s['court'] == $slotInfo['court'] && $s['date'] == $slotInfo['date'] && $s['slot'] == $slotInfo['slot']) {
                $existsKey = $key;
                break;
            }
        }

        if ($existsKey !== null) {
            unset($slots[$existsKey]);
            $slots = array_values($slots);
        } else {
            $slots[] = $slotInfo;
        }

        session(['selected_slots' => $slots]);
        return response()->json($slots);
    }

    public function removeSlot(Request $request)
    {
        $slots = session('selected_slots', []);
        unset($slots[$request->index]);
        session(['selected_slots' => array_values($slots)]);

        return response()->json(array_values($slots));
    }

    // Hàm nhận dữ liệu từ form và đưa đến trang thanh toán
    public function payments(Request $request)
    {
        // Dữ liệu slots gửi từ form, dưới dạng JSON
        $slots = json_decode($request->slots, true);
        // CHUYỂN MẢNG $slots SANG COLLECTION ĐỂ SỬ DỤNG CÁC HÀM CỦA LARAVEL
        $slotCollection = collect($slots);

        // LẤY CÁC GIÁ TRỊ DUY NHẤT
        $uniqueCourts = $slotCollection->pluck('court')->unique()->implode(' , ');
        $uniqueDates = $slotCollection->pluck('date')->unique()->implode(' / ');

        // LẤY THỜI GIAN ĐẶT DUY NHẤT (start_time và end_time)
        // Để giữ nguyên định dạng "start_time đến end_time", ta phải tạo một chuỗi tạm
        $uniqueTimes = $slotCollection->map(function ($slot) {
            return $slot['start_time'] . ' đến ' . $slot['end_time'];
        })->unique()->implode(' / ');

        $customer = Users::find($request->input('user_id'));
        $facilities = Facilities::find($request->input('facility_id'));
        $countSlots = count($slots);

        if ($countSlots % 2 === 0) {
            $result = ($countSlots / 2) . ' tiếng';
        } else {
            $result = (($countSlots - 1) / 2) . ' tiếng rưỡi';
        }
        // Truyền sang view thanh toán
        return view('payment', [
            'slots' => $slots,
            'result' => $result,
            'customer' => $customer,
            'facilities' => $facilities,
            // TRUYỀN CÁC GIÁ TRỊ DUY NHẤT ĐÃ XỬ LÝ
            'uniqueCourts' => $uniqueCourts,
            'uniqueDates' => $uniqueDates,
            'uniqueTimes' => $uniqueTimes,
        ]);
    }

    public function payments_complete(Request $request)
    {
        $slots = json_decode($request->input('slots'), true);
        $invoiceDetailId = $request->input('invoice_details_id');
        $userId = $request->input('user_id');
        $facility_id = $request->input('facility_id');

        $total = 0;
        foreach ($slots as $slot) {
            $total += $slot['price'];
        }

        DB::table(table: 'invoice_details')->insert([
            'invoice_detail_id' => $invoiceDetailId,
            'sub_total' => $total
        ]);
        if (!$slots || !is_array($slots)) {
            return back()->with('error', 'Không có dữ liệu đặt sân!');
        }

        // dd($slots, $invoiceDetailId, $userId, $facility_id);
        foreach ($slots as $slot) {
            DB::table(table: 'bookings')->insert([
                'invoice_detail_id' => $invoiceDetailId,
                'user_id' => $userId,
                'facility_id' => $facility_id,
                'court_id' => $slot['court'],
                'booking_date' => \Carbon\Carbon::parse($slot['date'])->format('Y-m-d'),
                'time_slot_id' => $slot['time_slot_id'],
                'unit_price' => $slot['price'],
            ]);
        }



        return redirect()->route('chi_tiet_san', ['idSan' => $facility_id])
            ->with('success', 'Thanh toán và đặt sân thành công!');
    }

    public function profile($id) // Tham số $id từ URL
    {
        $user = Users::findOrFail($id); // Tìm user theo ID
        
        // Bạn cần một view để hiển thị hồ sơ này
        return view('user.profile_page', compact('user')); 
    }
}
