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
        return view('index',compact('sancaulong'));
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
            'data'    => $sans,
            'offset'  => (int)$offset + $limit, // Cập nhật offset mới cho JS
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

        return view('venue-details',compact('thongtinsan'));
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

        // Lấy danh sách đặt sân
        $bookings = Bookings::where('facility_id', $idSan)
            ->whereIn('booking_date', $dates)
            ->get()
            ->groupBy(['booking_date', 'time_slot_id']);

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
        return view('venue-details', compact('thongtinsan', 'customer', 'timeSlots', 'dates', 'bookings', 'thuTiengViet', 'soLuongSan', 'dsSanCon'));
    }

    public function processBooking(Request $request)
    {
        // validate input cơ bản
        $data = $request->validate([
            // facility_id kiểu integer hoặc string tùy DB -> điều chỉnh luật validate
            'facility_id' => 'required',
            'court_id' => 'required',
            'booking_date' => 'required|date',
            'time_slot_id' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        // Lấy user hiện tại
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt sân.');
        }

        $facilityId = $data['facility_id'];
        $courtId = $data['court_id'];
        $bookingDate = Carbon::parse($data['booking_date'])->format('Y-m-d');
        $timeSlotId = $data['time_slot_id'];
        $unitPrice = $data['unit_price'];

        // Sinh invoice_detail_id (unique). Bạn có thể đổi format nếu muốn
        $invoiceDetailId = 'INVDET-' . time() . '-' . strtoupper(Str::random(6));

        try {
            // Dùng transaction để tránh race condition
            DB::beginTransaction();

            // Kiểm tra trùng: đã có booking cùng facility_id, court_id, booking_date, time_slot_id chưa?
            $exists = DB::table('bookings')
                ->where('facility_id', $facilityId)
                ->where('court_id', $courtId)
                ->where('booking_date', $bookingDate)
                ->where('time_slot_id', $timeSlotId)
                ->lockForUpdate() // giữ hàng để tránh race
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->with('error', 'Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác.');
            }

            // Lưu booking
            $bookingId = DB::table('bookings')->insertGetId([
                // Nếu booking_id là auto-increment, insertGetId sẽ trả về id
                'invoice_detail_id' => $invoiceDetailId,
                'user_id' => $userId,
                'facility_id' => $facilityId,
                'court_id' => $courtId,
                'booking_date' => $bookingDate,
                'time_slot_id' => $timeSlotId,
                'unit_price' => $unitPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Redirect hoặc trả JSON tuỳ bạn muốn
            return redirect()->back()->with('success', 'Đặt sân thành công. Mã đặt: ' . $bookingId);

        } catch (\Exception $e) {
            DB::rollBack();
            // log lỗi nếu cần: \Log::error($e);
            return back()->with('error', 'Có lỗi xảy ra khi đặt sân: ' . $e->getMessage());
        }
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
    foreach($slots as $key => $s){
        if($s['court']==$slotInfo['court'] && $s['date']==$slotInfo['date'] && $s['slot']==$slotInfo['slot']){
            $existsKey = $key;
            break;
        }
    }

    if($existsKey !== null){
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
        if($countSlots % 2 === 0)
        {
            $result = ($countSlots/2).' tiếng';
        }
        else 
        {
            $result = (($countSlots - 1)/2).' tiếng rưỡi'; 
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
}
