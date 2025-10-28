<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
class OwnerController extends Controller
{
    public function index()
    {
        // lấy dữ liệu KPI (doanh thu, đặt sân...) của CƠ SỞ NÀY
        // $revenue = ...
        // $bookings = ...
        // return view('owner.dashboard', compact('revenue', 'bookings'));
        //  Lấy thông tin cơ sở của chủ sân đang đăng nhập
        $owner = Auth::user();
        if (!$owner) {
            // Có thể chuyển hướng về trang login hoặc báo lỗi
            abort(401, 'Unauthorized');
        }

        $facility = Facility::withoutGlobalScopes()
            ->where('owner_id', $owner->user_id)
            ->first();

        $facilityStatusMessage = null;
        $facilityStatusType = 'info';

        // Kiểm tra trạng thái và tạo thông báo tương ứng
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

        // Truyền cả facility (có thể null) sang view nếu bạn muốn hiển thị thêm thông tin
        return view('owner.index', compact('facilityStatusMessage', 'facilityStatusType', 'facility'));
    }

    // Truyền biến thông báo và kiểu thông báo sang view
    //Hiển thị trang Quản lý Cơ sở (Đăng ký / Cập nhật sân)
    public function facility()
    {
        // lấy thông tin cơ sở của chủ sân này
        $facility = Facility::withoutGlobalScopes()
            ->where('owner_id', auth()->id())->first();
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
            // Thông tin chủ sân    
            'owner_phone' => 'nullable|string|max:20',        // SĐT chủ sân
            'owner_address' => 'nullable|string|max:255',   // Địa chỉ chủ sân
            'owner_cccd' => 'nullable|string|max:50|unique:users,CCCD,' . auth()->id() . ',user_id', // CCCD, unique trừ user hiện tại
        ]);

        // --- CHUẨN BỊ DỮ LIỆU ---
        $user = Auth::user(); // Lấy user đang đăng nhập
        $user->phone = $validatedData['owner_phone'];     // Cập nhật SĐT
        $user->address = $validatedData['owner_address']; // Cập nhật Địa chỉ
        $user->CCCD = $validatedData['owner_cccd'];       // Cập nhật CCCD
        $user->save(); // Lưu thay đổi vào bảng users
        $facilityData = [
            'facility_name' => $validatedData['facility_name'],
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'open_time' => $validatedData['open_time'],
            'close_time' => $validatedData['close_time'],
            'description' => $validatedData['description'],
            'status' => 'chờ duyệt',
        ];

        // --- XỬ LÝ UPLOAD FILE GIẤY PHÉP KD ---
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
            // Di chuyển file vào thư mục public/img/licenses với tên mới
            try {
                $file->move($destinationPath, $newFileName);
                $relativePath = 'img/licenses/' . $newFileName;
                // THÊM đường dẫn file vào $facilityData TRƯỚC KHI LƯU
                $facilityData['business_license_path'] = $relativePath;

                $existingFacility = Facility::withoutGlobalScopes()->where('owner_id', auth()->id())->first();
                if ($existingFacility && $existingFacility->business_license_path) {
                    $oldFilePath = public_path($existingFacility->business_license_path);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi upload Giấy phép KD: ' . $e->getMessage());
                return back()->withInput()->withErrors(['business_license' => 'Không thể lưu file tải lên. Vui lòng kiểm tra quyền ghi thư mục public/img/licenses.']);
            }
        }
        Facility::updateOrCreate(['owner_id' => auth()->id()], $facilityData);
        return redirect()->route('owner.index')
            ->with('success', 'Thông tin cơ sở đã được gửi đi chờ duyệt!');
    }
    // Hiển thị trang Quản lý Nhân viên
    public function staff()
    {
        // lấy danh sách nhân viên của cơ sở này
        // $staff = auth()->user()->facility->staff;
        // return view('owner.staff', compact('staff'));
        return view('owner.staff');
    }
}
