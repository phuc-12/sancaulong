<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Illuminate\Http\Request;
use App\Models\facilities;
use App\Models\Users;
use App\Models\Time_slots;
use Illuminate\Support\Facades\Auth;
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

        $thongtinsan = Facilities::get()->where('facility_id', $idSan)->first();

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

        return view('venue-details', compact('thongtinsan', 'customer', 'timeSlots', 'dates', 'bookings', 'thuTiengViet'));
    }

    public function bookingProcess(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'facility_id' => 'required',
            'time_slot_id' => 'required',
            'booking_date' => 'required|date',
        ]);

        Bookings::create([
            'user_id' => $request->maKH,
            'facility_id' => $request->maSan,
            'time_slot_id' => $request->time_slot_id,
            'booking_date' => $request->ngayDat,
            'ngayTao' => now(),
        ]);

        return redirect()->route('venue.show', $request->idSan)->with('success', 'Đặt sân thành công!');
    }

    // public function longtermStore(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email',
    //         'phonenumber' => 'required',
    //         'soluong' => 'required|integer',
    //         'date_start' => 'required|date',
    //         'date_end' => 'required|date|after:date_start',
    //         'comments' => 'nullable',
    //     ]);

    //     Contract::create([
    //         'maKH' => $request->maKH,
    //         'maSan' => $request->maSan,
    //         'ngayTao' => now(),
    //         'thoiGianBatDau' => $request->date_start,
    //         'thoiGianKetThuc' => $request->date_end,
    //         'soLuongSan' => $request->soluong,
    //         'ghiChu' => $request->comments,
    //         'trangThai' => 'chờ',
    //     ]);

    //     return redirect()->route('venue.show', $request->maSan)->with('success', 'Gửi yêu cầu thuê dài hạn thành công!');
    // }

    // public function profile($id)
    // {
    //     $user = Users::findOrFail($id);
    //     if (auth()->id() !== $user->id) {
    //         abort(403);
    //     }
    //     return view('user.profile', compact('user'));
    // }

    // public function myCourts()
    // {
    //     $courts = auth()->user()->courts; // giả sử có quan hệ
    //     return view('user.courts', compact('courts'));
    // }
}
