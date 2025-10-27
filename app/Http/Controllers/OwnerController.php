<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Facility;
use Storage;
class OwnerController extends Controller
{
    public function index()
    {
        // lấy dữ liệu KPI (doanh thu, đặt sân...) của CƠ SỞ NÀY
        // $revenue = ...
        // $bookings = ...
        // return view('owner.dashboard', compact('revenue', 'bookings'));
        return view('owner.index');
    }

    //Hiển thị trang Quản lý Cơ sở (Đăng ký / Cập nhật sân)
    public function facility()
    {
        // lấy thông tin cơ sở của chủ sân này
        $facility = Facility::where('owner_id', auth()->id())->first();
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
            'business_license' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048', 
        ]);

        // --- CHUẨN BỊ DỮ LIỆU (Đã đúng) ---
        $facilityData = [
            'facility_name' => $validatedData['facility_name'],
            'address'       => $validatedData['address'],
            'phone'         => $validatedData['phone'],
            'open_time'     => $validatedData['open_time'],   
            'close_time'    => $validatedData['close_time'],  
            'description'   => $validatedData['description'],
            'status'        => 'chờ duyệt', 
        ];

        // --- XỬ LÝ UPLOAD FILE GIẤY PHÉP KD ---
        if ($request->hasFile('business_license')) {
            $existingFacility = Facility::where('owner_id', auth()->id())->first(); 
            
            if ($existingFacility && $existingFacility->business_license_path) {
                Storage::disk('public')->delete($existingFacility->business_license_path);
            }
            
            $path = $request->file('business_license')->store('licenses', 'public'); 
            $facilityData['business_license_path'] = $path; 
        }

        // --- LƯU VÀO CSDL ---
        $facility = Facility::updateOrCreate(
            ['owner_id' => auth()->id()], 
            $facilityData                 
        );
        return redirect()->route('owner.facility')
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
