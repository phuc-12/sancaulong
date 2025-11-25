<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use App\Models\Courts;
use App\Models\Facility;
use Illuminate\Http\Request;
use App\Models\facilities;
use App\Models\Users;
use App\Models\Time_slots;
use App\Models\Court;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Mail\LongTermInvoiceMail;
class HomeController extends Controller
{
    const LIMIT_PER_LOAD = 10;
    public function index()
    {
        $sancaulong = Facilities::with('courtPrice')->where('status', 'đã duyệt')->get();
        // dd($sancaulong);
        return view('index', compact('sancaulong'));

    }

    public function listing_grid(Request $request)
    {
        $total_count = Facilities::where('status', 'đã duyệt')->count();
        $offset = $request->get('offset', 0);
        $limit = 9;

        $danhsachsan = Facilities::with('courtPrice')
            ->where('status', 'đã duyệt')
            ->skip($offset)
            ->take($limit)
            ->get();

        $hasMore = ($offset + $limit) < $total_count;

        return view('listing-grid', compact('danhsachsan', 'total_count', 'hasMore'));
    }

    public function loadMoreSan(Request $request)
    {
        $offset = $request->get('offset', 0);
        $limit = 9;

        $danhsachsan = Facilities::with('courtPrice')
            ->where('status', 'đã duyệt')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->map(function($item) {
                return [
                    'facility_id' => $item->facility_id,
                    'facility_name' => $item->facility_name,
                    'description' => $item->description,
                    'address' => $item->address,
                    'open_time' => $item->open_time,
                    'close_time' => $item->close_time,
                    'image' => asset($item->image),
                    'courtPrice' => [
                        'default_price' => $item->courtPrice->default_price ?? 0
                    ],
                ];
            });

        $total_count = Facilities::where('status', 'đã duyệt')->count();
        $hasMore = ($offset + $limit) < $total_count;

        return response()->json([
            'data' => $danhsachsan,
            'hasMore' => $hasMore
        ]);
    }

    public function show(Request $request)
    {
        $idSan = $request->input('facility_id');
        // Lấy thông tin sân
        $thongtinsan = Facilities::where('facility_id', $idSan)->firstOrFail();
        // Nếu chưa đăng nhập → chuyển về trang login
        if (!Auth::check()) {
            session([
                'url.intended' => route('chi_tiet_san_get', ['facility_id' => $idSan])
            ]);
            return redirect()->route('login');
        }

        // Nếu đã đăng nhập → lấy thông tin customer
        $customer = Users::where('user_id', Auth::id())->first();

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

        $success_message = $request->input('success_message');
        // dd($customer->toArray());
        return view('venue-details', compact('thongtinsan', 'customer', 'timeSlots', 'dates', 'bookingsData', 'thuTiengViet', 'soLuongSan', 'dsSanCon', 'success_message'));
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
        $slots = json_decode($request->slots, true);
        $slotCollection = collect($slots);

        $uniqueCourts = $slotCollection->pluck('court')->unique()->implode(' , ');
        $uniqueDates = $slotCollection->pluck('date')->unique()->implode(' / ');
        $uniqueTimes = $slotCollection->map(function ($slot) {
            return $slot['start_time'] . ' đến ' . $slot['end_time'];
        })->unique()->implode(' / ');

        $customer = Users::find($request->input('user_id'));
        $facilities = Facilities::find($request->input('facility_id'));
        $countSlots = count($slots);

        $tempCustomer = [
            'user_id' => $customer->user_id, // thêm user_id để không lỗi
            'fullname' => $request->input('fullname') ?: $customer->fullname,
            'phone' => $request->input('phone') ?: $customer->phone,
            'email' => $request->input('email') ?: $customer->email,
        ];
        if ($countSlots % 2 === 0) {
            $result = ($countSlots / 2) . ' tiếng';
        } else {
            $result = (($countSlots - 1) / 2) . ' tiếng rưỡi';
        }

        // ===== Lọc promotions theo ngày đặt sân =====
        $bookingDates = $slotCollection->pluck('date')->map(function($d) {
            return Carbon::createFromFormat('d-m-Y', $d)->format('Y-m-d');
        })->unique();

        $promotions = DB::table('promotions')->where('status', 1)
        ->where('facility_id',$request->input('facility_id'))
        ->where(function($query) use ($bookingDates) {
            foreach($bookingDates as $date) {
                $query->orWhere(function($q) use ($date) {
                    $q->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
                });
            }
        })
        ->get();

        // dd($bookingDates, $promotions);
        // Truyền sang view thanh toán
        return view('payment', [
            'slots' => $slots,
            'result' => $result,
            'customer' => (object) $tempCustomer,
            'facilities' => $facilities,

            // TRUYỀN CÁC GIÁ TRỊ DUY NHẤT ĐÃ XỬ LÝ
            'uniqueCourts' => $uniqueCourts,
            'uniqueDates' => $uniqueDates,
            'uniqueTimes' => $uniqueTimes,
            'promotions' => $promotions, // thêm để hiển thị dropdown
        ]);
    }

    public function payments_complete(Request $request)
    {
        $slots = json_decode($request->input('slots'), true);
        $invoiceDetailId = $request->input('invoice_details_id');
        $userId = $request->input('user_id');
        $facility_id = $request->input('facility_id');
        $total_final = $request->input('total_final');
        $promotion_id = $request->promotion_id ?? null;
        $description = DB::table('promotions')->where('promotion_id',$promotion_id)
        ->select('description')
        ->first();
        $fullname = $request->input('customer_name');
        $phone = $request->input('customer_phone');
        $email = $request->input('customer_email');

        $currentUser = Users::find($userId);

        if ($currentUser) {
            // Nếu phone khác với trong DB thì tạo user mới
            if ($currentUser->phone !== $phone) {
                $newUser = Users::create([
                    'fullname' => $fullname,
                    'phone' => $phone,
                    'email' => $email,
                    'password' => bcrypt('123456789'), // hoặc tạo password mặc định
                    'role_id' => '5', // ví dụ
                ]);
                $userId = $newUser->user_id;
            }
        } else {
            // Nếu user không tồn tại thì cũng tạo mới
            $newUser = Users::create([
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'password' => bcrypt('123456789'), // hoặc tạo password mặc định
                'role_id' => '5', // ví dụ
            ]);
            $userId = $newUser->user_id;
        }
        $user_id = Users::where('user_id',$userId)->get();
        $total = 0;
        foreach ($slots as $slot) {
            $total += $slot['price'];
        }
        $payment_method = 1;
        $payment_status = 'Đã thanh toán';
        DB::table(table: 'invoice_details')->insert([
            'invoice_detail_id' => $invoiceDetailId,
            'sub_total' => $total,
            'facility_id' => $facility_id,
        ]);
        $invoice_details = DB::table('invoice_details')
            ->select('invoice_id')
            ->where('invoice_detail_id', $invoiceDetailId)
            ->first();
        $total_final = $request->total_final;
        
        
        DB::table(table: 'invoices')->insert([
            'invoice_id' => $invoice_details->invoice_id,
            'customer_id' => $userId,
            'issue_date' => now(),
            'total_amount' => $total,
            'promotion_id' => $promotion_id,
            'final_amount' => $total_final,
            'payment_status' => $payment_status,
            'payment_method' => $payment_method,
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
                'status' => 'Đã thanh toán (Online)'
            ]);
        }

        // 1. Chuẩn bị dữ liệu PDF
    $slots = json_decode($request->input('slots'), true);
    $total = array_sum(array_column($slots, 'price'));
    $customer = Users::find($userId);
    $facilities = Facilities::find($facility_id);

    // Tính thời gian tổng
    $countSlots = count($slots);
    $result = ($countSlots % 2 === 0) ? ($countSlots / 2).' tiếng' : (($countSlots-1)/2).' tiếng rưỡi';

    $invoice_detail_id = $request->invoice_details_id;
    $invoice_details = DB::table('invoice_details')->where('invoice_detail_id',$invoice_detail_id)->first();

    $bank = $facilities->account_bank ?? 'VCB';
    $account = $facilities->account_no ?? '9704366899999';
    $accountName = $facilities->account_name ?? 'SAN CAU LONG DEMO';
    $qrUrl = "https://img.vietqr.io/image/{$bank}-{$account}-compact2.png?amount={$total}&addInfo=Thanh%20toan%20dat%20san&accountName=" . urlencode($accountName);
    // dd($slots);
    $pdf = PDF::setOptions(['isRemoteEnabled' => true])
              ->loadView('pdf', [
                  'slots' => $slots,
                  'result' => $result,
                  'customer' => $customer,
                  'facilities' => $facilities,
                  'invoice_detail_id' => $invoice_detail_id,
                  'total' => $total,
                  'description' => $description,
                  'total_final' => $total_final,
                  'user_id_nv' => null,
                  'fullname_nv' => null,
                  'invoice_time' => now()->format('d/m/Y H:i:s'),
                  'invoice_id' => $invoice_details->invoice_id,
                  'uniqueCourts' => collect($slots)->pluck('court_id')->unique()->implode(' , '),
                  'uniqueDates' => collect($slots)->pluck('date')->unique()->implode(' / '),
                  'uniqueTimes' => collect($slots)->map(fn($s) => $s['start_time'].' đến '.$s['end_time'])->unique()->implode(' / '),
                  'qrUrl' => $qrUrl,
              ])->output();
                // dd($customer->email, $pdf, $customer->fullname);
    // 2. Gửi email
    $pdfContent = $pdf; // nội dung PDF từ loadView
    $tempPath = storage_path('app/public/invoice_'.$invoice_detail_id.'.pdf');
    file_put_contents($tempPath, $pdfContent);

    Mail::to($customer->email)->send(new InvoiceMail($tempPath, $customer->fullname));

        return view('layouts.redirect_post', [
            'facility_id' => $facility_id,
            'user_id' => $userId,
            'success_message' => 'Thanh toán và đặt sân thành công! Giao dịch đã được lưu trong lịch đặt của bạn!!!'
        ]);

    }

    public function contract_bookings(Request $request)
    {
        $idSan = $request->input('facility_id');
        $thongtinsan = Facilities::where('facility_id', $idSan)->firstOrFail();
        $customer = Auth::check() ? Users::where('user_id', Auth::id())->first() : null;
        $timeSlots = Time_slots::all();
        // Lấy ngày bắt đầu và kết thúc từ form
        $dateStart = $request->input('date_start') ?? now()->format('Y-m-d');
        $dateEnd = $request->input('date_end') ?? now()->addDays(7)->format('Y-m-d');
        
        $fullname = $request->name;
        $phone = $request->phonenumber;
        
        // Sinh mảng ngày từ date_start → date_end
        $dates = [];
        $current = \Carbon\Carbon::parse($dateStart);
        $end = \Carbon\Carbon::parse($dateEnd);

        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Lấy danh sách đặt sân
        $bookings = Bookings::where('facility_id', $idSan)
            ->whereBetween('booking_date', [$dateStart, $dateEnd])
            ->get(['booking_date', 'time_slot_id', 'court_id']);

        $bookingsData = [];
        foreach ($bookings as $b) {
            $bookingsData[$b->booking_date][$b->time_slot_id][$b->court_id] = true;
        }

        $thuTiengViet = [
            'Mon' => 'Thứ hai',
            'Tue' => 'Thứ ba',
            'Wed' => 'Thứ tư',
            'Thu' => 'Thứ năm',
            'Fri' => 'Thứ sáu',
            'Sat' => 'Thứ bảy',
            'Sun' => 'Chủ nhật',
        ];

        $soLuongSan = $thongtinsan->quantity_court;
        $dsSanCon = [];
        for ($i = 1; $i <= $soLuongSan; $i++) {
            $dsSanCon[] = [
                'id' => $thongtinsan->facility_id . '-' . $i,
                'ten' => 'Sân ' . $i
            ];
        }
        $courts = Courts::where('facility_id', $idSan)->get();
        // Lấy các khuyến mãi hợp lệ cho sân và có discount_type = 'Khách hàng thân thiết'
        $promotions = DB::table('promotions')
        ->where('facility_id', $idSan)
        ->where('discount_type', 'customer')
        ->whereDate('start_date', '<=', $dateStart)
        ->whereDate('end_date', '>=', $dateStart)
        ->get();
        // dd($promotions);
        return view('contract', compact(
            'thongtinsan',
            'customer',
            'timeSlots',
            'dates',
            'bookingsData',
            'thuTiengViet',
            'soLuongSan',
            'dsSanCon',
            'dateStart',
            'dateEnd',
            'courts',
            'dateStart',
            'dateEnd',
            'fullname',
            'phone',
            'promotions',
        ));
    }

    public function contracts_preview(Request $request)
    {
        try {
            $data = $request->all();

            $startDate = $data['start_date'] ?? null;
            $endDate = $data['end_date'] ?? null;
            $dayOfWeeks = $data['day_of_weeks'] ?? [];
            $timeSlots = $data['time_slots'] ?? [];
            $courts = $data['courts'] ?? [];
            $actualDates = $data['actual_dates'] ?? [];
            $defaultPrice = $data['default_price'] ?? null;
            $specialPrice = $data['special_price'] ?? null;
            $facility_id = $request->input('facility_id');
            $user_id = $data['user_id'] ?? null;

            $promotion_id = $request->promotion_id;
            // Lấy các khuyến mãi hợp lệ cho sân và có discount_type = 'Khách hàng thân thiết'
            $promotions = DB::table('promotions')
            ->where('promotion_id', $promotion_id)
            ->get();
            // dd($promotions);
            if (!$startDate || !$endDate || empty($dayOfWeeks) || empty($timeSlots) || empty($courts) || empty($actualDates)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thiếu dữ liệu đầu vào!'
                ], 400);
            }
            $timeSlots = json_decode($data['time_slots'], true) ?? [];
            $actualDates = json_decode($data['actual_dates'], true) ?? [];
            $courts = json_decode($data['courts'], true) ?? [];
                // ================== KIỂM TRA TRÙNG (TỐI ƯU CHO DỮ LIỆU LỚN) ==================
            $combinations = [];
            foreach ($actualDates as $item) {
                $date = $item['date'] ?? null;
                $facilityId = $item['facility_id'] ?? ($facility_id ?? null); // thêm dòng này
                $courtIds = $item['courts'] ?? [];
                $timeSlotIds = $item['time_slots'] ?? [];

                if (!$date || !$facilityId || empty($courtIds) || empty($timeSlotIds)) continue;

                foreach ($courtIds as $courtId) {
                    foreach ($timeSlotIds as $timeSlotId) {
                        $combinations[] = [
                            'date' => $date,
                            'facility_id' => $facilityId,
                            'court_id' => $courtId,
                            'time_slot_id' => $timeSlotId,
                        ];
                    }
                }
            }

            if (empty($combinations)) {
                return back()->with('error', 'Không có ngày hợp lệ để kiểm tra!');
            }

            // Lấy danh sách booking trùng
            $existingBookings = DB::table('bookings')
                ->whereIn('booking_date', collect($combinations)->pluck('date')->unique())
                ->whereIn('facility_id', collect($combinations)->pluck('facility_id')->unique()) // thêm dòng này
                ->whereIn('court_id', collect($combinations)->pluck('court_id')->unique())
                ->whereIn('time_slot_id', collect($combinations)->pluck('time_slot_id')->unique())
                ->select('booking_date', 'facility_id', 'court_id', 'time_slot_id')
                ->get();

            $existingMap = [];
            foreach ($existingBookings as $b) {
                $existingMap["{$b->booking_date}_{$b->facility_id}_{$b->court_id}_{$b->time_slot_id}"] = true;
            }

            $conflicts = [];
            foreach ($combinations as $c) {
                $key = "{$c['date']}_{$c['facility_id']}_{$c['court_id']}_{$c['time_slot_id']}";
                if (isset($existingMap[$key])) {
                    $conflicts[] = $c;
                }
            }

            if (!empty($conflicts)) {
                
                $idSan = $facility_id;
                $thongtinsan = Facilities::where('facility_id', $idSan)->first();
                $customer = Users::where('user_id', $user_id)->first();
                $timeSlots = Time_slots::all();
                // Lấy ngày bắt đầu và kết thúc từ form
                $dateStart = isset($startDate) ? trim($startDate, '"') : now()->format('Y-m-d');
                $dateEnd   = isset($endDate)   ? trim($endDate, '"')   : now()->addDays(7)->format('Y-m-d');

                // Sinh mảng ngày từ date_start → date_end
                $dates = [];
                $current = \Carbon\Carbon::parse($dateStart);
                $end     = \Carbon\Carbon::parse($dateEnd);

                while ($current->lte($end)) {
                    $dates[] = $current->format('Y-m-d');
                    $current->addDay();
                }

                // Lấy danh sách đặt sân
                $bookings = Bookings::where('facility_id', $idSan)
                    ->whereBetween('booking_date', [$dateStart, $dateEnd])
                    ->get(['booking_date', 'time_slot_id', 'court_id']);

                $bookingsData = [];
                foreach ($bookings as $b) {
                    $bookingsData[$b->booking_date][$b->time_slot_id][$b->court_id] = true;
                }

                $thuTiengViet = [
                    'Mon' => 'Thứ hai', 'Tue' => 'Thứ ba', 'Wed' => 'Thứ tư',
                    'Thu' => 'Thứ năm', 'Fri' => 'Thứ sáu', 'Sat' => 'Thứ bảy', 'Sun' => 'Chủ nhật',
                ];

                $soLuongSan = $thongtinsan->quantity_court;
                $dsSanCon = [];
                for ($i = 1; $i <= $soLuongSan; $i++) {
                    $dsSanCon[] = [
                        'id' => $thongtinsan->facility_id . '-' . $i,
                        'ten' => 'Sân ' . $i
                    ];
                }
                $courts = Courts::where('facility_id', $idSan)->get();
                $fullname = $request->fullname;
                $phone = $request->phone;
                $conflictsGrouped = [];
                foreach ($conflicts as $c) {
                    $key = $c['date'].'_'.$c['court_id'];
                    $conflictsGrouped[$key]['date'] = $c['date'];
                    $conflictsGrouped[$key]['court_id'] = $c['court_id'];
                    $conflictsGrouped[$key]['time_slots'][] = $c['time_slot_id'];
                }

                // Tạo array gọn
                $conflicts = array_values($conflictsGrouped);
                return view('contract', compact(
                    'thongtinsan', 'customer', 'timeSlots', 'dates',
                    'bookingsData', 'thuTiengViet', 'soLuongSan', 'dsSanCon',
                    'dateStart', 'dateEnd', 'courts','fullname','phone', 'promotions'
                ))->with([
                    'message' => 'Có khung giờ đã được đặt trước, vui lòng chọn lại!',
                    'conflicts' => $conflicts // <--- phải truyền conflicts vào view
                ]);

            }


        // =====================================================================
            // === XỬ LÝ KHUNG GIỜ ===
            $timeSlots = collect($timeSlots)->slice(0, -1);
            // $actualDates = collect($actualDates)->slice(0, -1);
            $slotDetails = $timeSlots->map(function ($slot) use ($defaultPrice, $specialPrice) {
                $start = \Carbon\Carbon::parse($slot['start']);
                $end = \Carbon\Carbon::parse($slot['end']);
                $duration = $start->floatDiffInMinutes($end) / 60;
                $startDecimal = $start->hour + $start->minute / 60;
                $pricePerHour = ($startDecimal >= 16) ? $specialPrice : $defaultPrice;
                $amount = $pricePerHour * $duration;

                return [
                    'start' => $slot['start'],
                    'end' => $slot['end'],
                    'hours' => $duration,
                    'price_per_hour' => $pricePerHour,
                    'amount' => $amount
                ];
            });

            // === TÍNH TỔNG ===
            $totalDays = count($actualDates);
            $totalCourts = count($courts);
            $totalAmount = $slotDetails->sum('amount') * $totalDays * $totalCourts;
            if($request->fullname && $request->phone)
            {
                $fullname = $request->fullname;
                $phone = $request->phone;
            }
            else 
            {
                $user = DB::table('users')->get()->where('user_id', $user_id)->first();
            }
            
            $facilities = Facilities::with('Users')->get()->where('facility_id', $facility_id)->first();
            $startDate = isset($startDate) ? trim($startDate, '"') : now()->format('Y-m-d');
            $endDate = isset($endDate) ? trim($endDate, '"') : now()->addDays(7)->format('Y-m-d');
            $dayOfWeeks = $dayOfWeeks ?? [];
            // Nếu là chuỗi JSON, decode
            if (is_string($dayOfWeeks)) {
                $dayOfWeeks = json_decode($dayOfWeeks, true);
                if (!is_array($dayOfWeeks)) {
                    $dayOfWeeks = [];
                }
            }
            // Tạo mảng lines hiển thị trong bảng
            $lines = [];
            foreach ($actualDates as $dateItem) {
                $date = \Carbon\Carbon::parse($dateItem['date'])->format('d/m');

                foreach ($courts as $court) {
                    // Tính tổng amount cho sân này ngày này
                    $totalAmountCourt = collect($slotDetails)->sum('amount');
                    $totalDuration = collect($slotDetails)
                        ->map(fn($slot) => is_array($slot) ? $slot['hours'] ?? 0 : ($slot->hours ?? 0))
                        ->sum();

                    $lines[] = [
                        'date' => $date,
                        'court' => $court,
                        'duration' => $totalDuration,
                        'amount' => $totalAmountCourt,
                    ];
                }
            }
            // Tính tổng
            $totalAmount = collect($lines)->sum('amount');
            
            $promotion = $promotions->first(); // Lấy object đầu tiên

            $final_amount = $totalAmount;
            if ($promotion) {
                $final_amount = $totalAmount - $totalAmount * ($promotion->value ?? 0);
            }
            // Tạo summary gọn
            $summary = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'selected_days' => $dayOfWeeks,
                'total_days' => $totalDays,
                'total_slots' => $slotDetails->count(),
                'total_courts' => $totalCourts,
                'total_amount' => $totalAmount,
                'final_amount' => $final_amount,
                'promotions' => $promotion,
            ];

            // Thông tin user/facility
            $userInfo = [
                'user_id' => $user_id ?? '---',
                'user_name' => $user->fullname ?? $fullname,
                'phone' => $user->phone ?? $phone,
                'facility_name' => $facilities->facility_name ?? '---',
                'facility_address' => $facilities->address ?? '---',
                'facility_phone' => $facilities->phone ?? '---',
                'facility_id' => $facility_id ?? '---',
                'account_name' => $facilities->account_name ?? '---',
                'account_no' => $facilities->account_no ?? '---',
                'account_bank' => $facilities->account_bank ?? '---',
            ];
            
            $details = [
                'actual_dates' => $actualDates,
                'slot_details' => $slotDetails,
                'courts' => $courts,
            ];

            return view('payment_contract', compact('summary', 'details', 'lines', 'userInfo'));

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function payments_contract_complete(Request $request)
    {
        $details = json_decode($request->input('details'), true);
        $slot_details = json_decode($request->input('slot_details'), true);
        $invoiceDetailId = $request->input('invoice_details_id');
        $userId = $request->input('user_id');
        $facility_id = $request->input('facility_id');
        $total_amount = $request->input('total_amount');
        $total = $request->input('tongtien');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        // dd($details,$slot_details);
        $fullname = $request->input('fullname');
        $phone = $request->input('phone');

        $currentUser = Users::where('phone', $phone)
        ->where('fullname', 'like', "%$fullname%")
        ->first();

        if (!$currentUser) {
            $newUser = Users::create([
                'fullname' => $fullname,
                'phone' => $phone,
                'password' => bcrypt('123456789'), // hoặc tạo password mặc định
                'role_id' => '5', // ví dụ
            ]);
            $userId = $newUser->user_id;
        } else {
            $userId = $currentUser->user_id;
        }

        if (!$details || !$slot_details) {
            return back()->with('error', 'Dữ liệu không hợp lệ!');
        }

        // Kiểm tra trùng invoice_detail_id
        if (!DB::table('invoice_details')->where('invoice_detail_id', $invoiceDetailId)->exists()) {
            DB::table('invoice_details')->insert([
                'invoice_detail_id' => $invoiceDetailId,
                'sub_total' => $total,
                'facility_id' => $facility_id,
            ]);
        }
        $promotion_id = $request->promotion_id;
        $payment_method = 1;
        $payment_status = 'Đã thanh toán';
        $deposit = 0;
        $note = null;
        DB::table(table: 'long_term_contracts')->insert([
            'invoice_detail_id' => $invoiceDetailId,
            'customer_id' => $userId,
            'issue_date' => now(),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_amount' => $total_amount,
            'promotion_id' => $promotion_id,
            'final_amount' => $total,
            'payment_status' => $payment_status,
            'deposit' => $deposit,
            'note' => $note,
        ]);
        // Chèn từng chi tiết đặt sân
        foreach ($details as $detail) {
            $date = \Carbon\Carbon::parse($detail['date'])->format('Y-m-d');

            foreach ($detail['courts'] as $courtId) {
                foreach ($detail['time_slots'] as $timeSlotId) {
                    // Lấy đơn giá tương ứng (hoặc lấy 1 giá chung)
                    $amount = $slot_details[0]['amount'] ?? 0;

                    DB::table('bookings')->insert([
                        'invoice_detail_id' => $invoiceDetailId,
                        'user_id' => $userId,
                        'facility_id' => $facility_id,
                        'court_id' => $courtId,
                        'booking_date' => $date,
                        'time_slot_id' => $timeSlotId,
                        'unit_price' => $amount,
                    ]);
                }
            }
        }
        //======================================================
        $contract = DB::table('long_term_contracts')
            ->where('invoice_detail_id', $invoiceDetailId)
            ->first();

        $customer = Users::where('user_id', $userId)->first();
        $facilities = DB::table('facilities')->where('facility_id', $facility_id)->first();

        // Lấy các slot booking cho hợp đồng này
        $slots = [];
        foreach ($details as $detail) {
            $date = $detail['date'];
            foreach ($detail['courts'] as $courtId) {
                foreach ($detail['time_slots'] as $timeSlotIndex => $timeSlotId) {
                    // Lấy thông tin tương ứng từ $slot_details
                    $slotInfo = $slot_details[$timeSlotIndex] ?? null;
                    if($slotInfo){
                        $slots[] = [
                            'courts' => $courtId,
                            'booking_date' => $date,
                            'start_time' => $slotInfo['start'],
                            'end_time' => $slotInfo['end'],
                            'price' => $slotInfo['amount'],
                        ];
                    }
                }
            }
        }
        $promotions = DB::table('promotions')
        ->where('promotion_id',$promotion_id)
        ->first();
        // Lấy danh sách sân duy nhất
        $courts = collect($slots)->pluck('courts')->unique()->toArray();
        // Chuyển mảng thành chuỗi để hiển thị
        $courtsString = implode(', ', $courts);
        $pdf = PDF::loadView('long_term_contract_pdf', [
            'facilities' => $facilities,
            'customer' => $customer,
            'contract' => $contract,
            'slots' => $slots,
            'courts' => $courtsString, // truyền chuỗi
            'promotions' => $promotions,
        ]);

        if($customer->email){
            Mail::to($customer->email)->send(new LongTermInvoiceMail($customer->fullname, $pdf));
        }
        //=========================================================
        $userMana = $request->input('user_id');
        $checkMana = Users::where('user_id', $userMana)
        ->select('role_id')
        ->first();
        // dd($userMana, $checkMana);
        if($checkMana->role_id === 4)
        {
            return redirect()->route('manager.contracts')->with('success', 'Thành công!');
        }
        else 
        {
            return view('layouts.redirect_post', [
                'facility_id' => $facility_id,
                'user_id' => $userId,
                'success_message' => 'Thanh toán và đặt sân cố định thành công!!!'
            ]);
        }
    }

    public function list_Invoices(Request $request)
    {
        // Lấy user_id từ query string GET hoặc từ POST
        $user_id = $request->query('user_id', $request->user_id);

        // Lấy invoices của user, paginate 10 bản ghi/trang
        $invoices = DB::table('invoices')
            ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
            ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
            ->join('users', 'users.user_id', '=', 'invoices.customer_id')
            ->where('invoices.customer_id', $user_id)
            ->select(
                'invoices.*',
                'facilities.facility_name as facility_name',
                'users.fullname as fullname',
                'invoices.issue_date as issue_date',
                'invoices.final_amount as final_amount',
                'invoice_details.invoice_detail_id as invoice_detail_id',
                'invoice_details.facility_id as facility_id'
            )
            ->orderBy('invoices.invoice_id', 'desc')
            ->paginate(10)
            ->appends(['user_id' => $user_id]); // giữ user_id khi bấm trang 2

        $success_message = $request->query('success_message', null); // nếu có thông báo

        // Build mybooking_details cho các invoice trên trang hiện tại
        $mybooking_details = [];
        foreach ($invoices as $invoice) {
            $details = DB::table('bookings')
                ->join('invoice_details', 'invoice_details.invoice_detail_id', '=', 'bookings.invoice_detail_id')
                ->join('time_slots', 'time_slots.time_slot_id', '=', 'bookings.time_slot_id')
                ->where('invoice_details.invoice_detail_id', $invoice->invoice_detail_id)
                ->select(
                    'bookings.*',
                    'time_slots.start_time',
                    'time_slots.end_time'
                )
                ->get();

            $mybooking_details[$invoice->invoice_detail_id] = $details;
        }

        // Trả về view
        return view('my_bookings', compact('user_id', 'invoices', 'mybooking_details', 'success_message'));
    }
    public function list_Contracts(Request $request)
    {
        $user_id = $request->user_id;
        $success_message = $request->success_message;

        // Lấy hợp đồng dài hạn của user, phân trang 10 bản ghi/trang
        $long_term_contracts = DB::table('long_term_contracts')
            ->join('invoice_details', 'long_term_contracts.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
            ->join('facilities', 'facilities.facility_id', '=', 'invoice_details.facility_id')
            ->join('users', 'users.user_id', '=', 'long_term_contracts.customer_id')
            ->where('long_term_contracts.customer_id', $user_id)
            ->select(
                'long_term_contracts.*',
                'facilities.facility_name as facility_name',
                'users.fullname as fullname',
                'long_term_contracts.issue_date as issue_date',
                'long_term_contracts.final_amount as final_amount'
            )
            ->orderBy('long_term_contracts.issue_date', 'desc')
            ->paginate(10)
            ->appends(['user_id' => $user_id]); // giữ user_id khi bấm trang 2

        // Lấy danh sách invoice_detail_id của trang hiện tại
        $invoiceIds = $long_term_contracts->pluck('invoice_detail_id')->toArray();

        // Lấy chi tiết booking cho tất cả invoice_detail_id trong trang
        $mycontract_details = DB::table('bookings')
            ->join('time_slots', 'time_slots.time_slot_id', '=', 'bookings.time_slot_id')
            ->whereIn('bookings.invoice_detail_id', $invoiceIds)
            ->select(
                'bookings.*',
                'time_slots.start_time',
                'time_slots.end_time'
            )
            ->get()
            ->groupBy('invoice_detail_id'); // nhóm theo invoice_detail_id để view dễ dùng

        // Trả về view
        return view('my_contracts', compact('user_id', 'long_term_contracts', 'mycontract_details', 'success_message'));
    }
    public function search(Request $request)
    {
        // 1. Lấy từ khóa tìm kiếm từ URL (?keyword=...)
        $keyword = $request->input('keyword');

        // 2. Kiểm tra nếu keyword rỗng thì quay về trang chủ
        if (empty($keyword)) {
            return redirect()->route('danh_sach_san');
        }

        // 3. Truy vấn với JOIN giữa facilities và court_prices
        $danhsachsan = Facilities::query()
            ->select(
                'facilities.facility_id',
                'facilities.facility_name',
                'facilities.address',
                'facilities.status',
                'facilities.image',
                'facilities.description',
                'facilities.open_time as open_time',
                'facilities.close_time as close_time',
            )
            ->where('facilities.status', 'đã duyệt')
            ->where(function ($query) use ($keyword) {
                $query->where('facilities.facility_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('facilities.address', 'LIKE', "%{$keyword}%");
            })
            ->get();

        $total_count = Facilities::query()
            ->where('facilities.status', 'đã duyệt')
            ->where(function ($query) use ($keyword) {
                $query->where('facilities.facility_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('facilities.address', 'LIKE', "%{$keyword}%");
            })->count();
        // dd($danhsachsan);
        // 4. Trả về view hiển thị kết quả
        return view('listing-grid', compact('danhsachsan', 'keyword','total_count'));
    }



    public function invoice_details(Request $request)
    {
        $slots = json_decode($request->slots, true);
        $slotCollection = collect($slots);
        $invoice_detail_id = $request->invoice_detail_id;
        $invoices = DB::table('invoices')
        ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
        ->join('promotions','promotions.promotion_id','=','invoices.promotion_id')
        ->where('invoice_details.invoice_detail_id', $invoice_detail_id)
        ->select(
            'invoices.*',
            'invoices.final_amount as final_amount',
            'promotions.*',
            'promotions.value as value',
            'promotions.description as description',

            )
        ->first();

        // dd($invoices);
        $uniqueCourts = $slotCollection->pluck('court_id')->unique()->implode(' , ');
        $uniqueDates = $slotCollection->pluck('booking_date')->unique()->implode(' / ');
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
        return view('invoice_details', [
            'slots' => $slots,
            'result' => $result,
            'customer' => $customer,
            'facilities' => $facilities,
            'invoice_detail_id' => $invoice_detail_id,
            'invoices' => $invoices,
            // TRUYỀN CÁC GIÁ TRỊ DUY NHẤT ĐÃ XỬ LÝ
            'uniqueCourts' => $uniqueCourts,
            'uniqueDates' => $uniqueDates,
            'uniqueTimes' => $uniqueTimes,
        ]);
    }

    public function cancel_invoice(Request $request)
    {
        $invoice_detail_id = $request->invoice_detail_id;
        $user_id = $request->user_id;

        Bookings::where('invoice_detail_id', $invoice_detail_id)->delete();

        $invoice_details = DB::table('invoice_details')
        ->where('invoice_detail_id', $invoice_detail_id)
        ->select('invoice_id')
        ->first();

        DB::table('invoices')->where('invoice_id', $invoice_details->invoice_id)->update([
            'payment_status' => 'Đã Hủy',
            'updated_at' => now(),
        ]);

        return view('layouts.redirect_mybookings', [
            'user_id' => $user_id,
            'success_message' => 'Đã hủy!!! Vui lòng liên hệ sân để hoàn tiền.',
        ]);
    }

    public function contract_details(Request $request)
    {
        $slots = json_decode($request->slots, true);
        $slotCollection = collect($slots);
        $invoice_detail_id = $request->invoice_detail_id;
        
        $long_term_contracts = DB::table('long_term_contracts')
        ->join('invoice_details','invoice_details.invoice_detail_id', '=','long_term_contracts.invoice_detail_id')
        ->join('promotions','promotions.promotion_id','=','long_term_contracts.promotion_id')
        ->where('long_term_contracts.invoice_detail_id',$invoice_detail_id)
        ->select(
            'long_term_contracts.*',
            'invoice_details.facility_id as facility_id',
            'promotions.*',
            'promotions.value as value',
            'promotions.description as description',
        )
        ->first();
        // dd($long_term_contracts);
        $customer = DB::table('users')
        ->where('user_id',$long_term_contracts->customer_id)
        ->first();
        
        $facilities = DB::table('facilities')
        ->where('facility_id',$long_term_contracts->facility_id)
        ->first();
        
        $groupedSlots = collect($slots)
        ->groupBy(function ($item) {
            return $item['booking_date'] . '_' . $item['court_id'];
        })
        ->map(function ($group) {
            $first = $group->first();
            return [
                'booking_date' => $first['booking_date'],
                'court_id' => $first['court_id'],
                // mỗi slot là 0.5 giờ → tổng giờ = số slot * 0.5
                'total_duration' => count($group) * 0.5,
                // tổng tiền cộng dồn
                'total_price' => collect($group)->sum('unit_price'),
            ];
        })
        ->values();
        $daysOfWeek = $groupedSlots
        ->map(function ($slot) {
            $date = Carbon::parse($slot['booking_date']);
            return match ($date->dayOfWeek) {
                0 => 'Chủ nhật',
                1 => 'Thứ 2',
                2 => 'Thứ 3',
                3 => 'Thứ 4',
                4 => 'Thứ 5',
                5 => 'Thứ 6',
                6 => 'Thứ 7',
            };
        })
        ->unique() // loại trùng
        ->values()
        ->implode(', '); // nối thành chuỗi "Thứ 2, Thứ 4, Thứ 6"

        // ✅ Lấy danh sách các sân duy nhất
        $courts = $groupedSlots
        ->pluck('court_id')
        ->unique()
        ->map(fn($id) => 'Sân ' . $id)
        ->implode(', ');

        return view('contract_details',[
            'slots' => $groupedSlots,
            'long_term_contracts' => $long_term_contracts,
            'customer' => $customer,
            'facilities' => $facilities,
            'daysOfWeek' => $daysOfWeek, // ✅ truyền ra view
            'courts' => $courts,
        ]);
    }

    public function cancel_contract(Request $request)
    {
        $invoice_detail_id = $request->invoice_detail_id;
        $user_id = $request->user_id;

        Bookings::where('invoice_detail_id', $invoice_detail_id)->delete();

        DB::table('long_term_contracts')->where('invoice_detail_id', $invoice_detail_id)->update([
            'payment_status' => 'Đã Hủy',
            'updated_at' => now(),
        ]);

        return view('layouts.redirect_mycontracts', [
            'user_id' => $user_id,
            'success_message' => 'Đã hủy hợp đồng!!! Vui lòng liên hệ sân để hoàn tiền.',
        ]);
    }

}

