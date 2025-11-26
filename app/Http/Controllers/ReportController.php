<?php
// app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Exports\BookingsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\Facilities;
use App\Models\Courts;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     *  HÀM HELPER: Lấy ID cơ sở duy nhất của chủ sân
     */
    private function getOwnerFacilityId($ownerId)
    {
        // Logic: 1 chủ sân (owner) chỉ quản lý 1 cơ sở (facility)
        $facility = Facilities::where('owner_id', $ownerId)->first();
        return $facility ? $facility->facility_id : null;
    }

    /**
     * Hiển thị trang dashboard
     */
    public function index()
    {
        $ownerId = Auth::id();
        $facility_id = $this->getOwnerFacilityId($ownerId);
        $courts = collect(); // Mặc định là rỗng

        if ($facility_id) {
            // Lấy tất cả sân con của cơ sở này
            $courts = DB::table('courts')
                ->where('facility_id', $facility_id)
                ->where('status', '1') // Chỉ lấy sân đang hoạt động
                ->orderBy('court_id', 'asc')
                ->get(['court_id', 'court_name']);

            Log::info('Courts loaded:', ['facility_id' => $facility_id, 'count' => $courts->count()]);
        }

        return view('owner.report.dashboard', compact('courts', 'facility_id'));
    }

    /**
     * API trả dữ liệu KPI
     */
    public function kpiData(Request $request)
    {
        $ownerId = Auth::id();
        $ownerFacilityId = $this->getOwnerFacilityId($ownerId);
        if (!$ownerFacilityId)
            return response()->json([], 404);

        $range = $request->query('range', 'month');
        $courtId = $request->query('court', 'all'); // Chỉ lấy courtId
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($range === 'custom' && $startDate && $endDate) {
            $dateRange = ['start' => $startDate, 'end' => $endDate];
            $previousRange = $this->getCustomPreviousRange($startDate, $endDate);
        } else {
            $dateRange = $this->getDateRange($range);
            $previousRange = $this->getPreviousDateRange($range);
        }

        $currentData = $this->calculateKPI($ownerId, $dateRange, $ownerFacilityId, $courtId);
        $previousData = $this->calculateKPI($ownerId, $previousRange, $ownerFacilityId, $courtId);

        $kpiData = [
            'revenue' => ['value' => $currentData['revenue'], 'change' => $this->calculateChange($currentData['revenue'], $previousData['revenue'])],
            'bookings' => ['value' => $currentData['bookings'], 'change' => $this->calculateChange($currentData['bookings'], $previousData['bookings'])],
            'bookings_individual' => ['value' => $currentData['bookings_individual'], 'change' => $this->calculateChange($currentData['bookings_individual'], $previousData['bookings_individual'] ?? 0)], // Đặt lẻ
            'bookings_contract' => ['value' => $currentData['bookings_contract'], 'change' => $this->calculateChange($currentData['bookings_contract'], $previousData['bookings_contract'] ?? 0)], // Hợp đồng
            'utilization' => ['value' => $currentData['utilization'], 'change' => $this->calculateChange($currentData['utilization'], $previousData['utilization'])],
            'customers' => ['value' => $currentData['customers'], 'change' => $this->calculateChange($currentData['customers'], $previousData['customers'])],
            'avgPrice' => ['value' => $currentData['avgPrice'], 'change' => $this->calculateChange($currentData['avgPrice'], $previousData['avgPrice'])],
            'growth' => ['value' => $this->calculateChange($currentData['revenue'], $previousData['revenue']), 'change' => 0]
        ];

        return response()->json($kpiData);
    }

    /**
     * API trả dữ liệu biểu đồ doanh thu theo thời gian (Line Chart)
     */
    public function revenueChart(Request $request)
    {
        $ownerId = Auth::id();
        $ownerFacilityId = $this->getOwnerFacilityId($ownerId);
        if (!$ownerFacilityId)
            return response()->json([], 404);

        $range = $request->query('range', 'month');
        $courtId = $request->query('court', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($range === 'custom' && $startDate && $endDate) {
            $dateRange = ['start' => $startDate, 'end' => $endDate];
        } else {
            $dateRange = $this->getDateRange($range);
        }

        $query = DB::table('bookings')
            ->where('facility_id', $ownerFacilityId)
            ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']]);

        if ($courtId !== 'all') {
            $query->where('bookings.court_id', $courtId);
        }

        $revenueData = $query
            ->select(DB::raw('DATE(bookings.booking_date) as date'), DB::raw('SUM(bookings.unit_price) as revenue'), DB::raw('COUNT(*) as bookings'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $labels = $revenueData->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('d/m');
        })->toArray();
        $revenues = $revenueData->pluck('revenue')->toArray();
        $bookings = $revenueData->pluck('bookings')->toArray();

        return response()->json(['labels' => $labels, 'revenues' => $revenues, 'bookings' => $bookings]);
    }

    /**
     * API trả dữ liệu đặt sân theo giờ (Bar Chart)
     */
    public function bookingsByHour(Request $request)
    {
        $ownerId = Auth::id();
        $ownerFacilityId = $this->getOwnerFacilityId($ownerId);
        if (!$ownerFacilityId)
            return response()->json([], 404);

        $range = $request->query('range', 'month');
        $courtId = $request->query('court', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($range === 'custom' && $startDate && $endDate) {
            $dateRange = ['start' => $startDate, 'end' => $endDate];
        } else {
            $dateRange = $this->getDateRange($range);
        }

        // Query đặt lẻ (từ invoices)
        $individualQuery = DB::table('bookings')
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.time_slot_id')
            ->join('invoice_details', 'bookings.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.invoice_id')
            ->where('bookings.facility_id', $ownerFacilityId)
            ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']]);

        // Query hợp đồng (từ long_term_contracts)
        $contractQuery = DB::table('bookings')
            ->join('time_slots', 'bookings.time_slot_id', '=', 'time_slots.time_slot_id')
            ->join('long_term_contracts', 'bookings.invoice_detail_id', '=', 'long_term_contracts.invoice_detail_id')
            ->where('bookings.facility_id', $ownerFacilityId)
            ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']])
            ->where('long_term_contracts.payment_status', 'like', '%thanh toán%');

        if ($courtId !== 'all') {
            $individualQuery->where('bookings.court_id', $courtId);
            $contractQuery->where('bookings.court_id', $courtId);
        }

        // Group by giờ cho đặt lẻ
        $individualData = $individualQuery
            ->select(DB::raw('HOUR(time_slots.start_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        // Group by giờ cho hợp đồng
        $contractData = $contractQuery
            ->select(DB::raw('HOUR(time_slots.start_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        // Fill từ 5h đến 23h
        $labels = [];
        $individualCounts = [];
        $contractCounts = [];
        for ($i = 5; $i <= 23; $i++) {
            $labels[] = $i . ':00';
            $individualCounts[] = $individualData[$i] ?? 0;
            $contractCounts[] = $contractData[$i] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'individual' => $individualCounts,
            'contract' => $contractCounts
        ]);
    }

    /**
     * API trả dữ liệu doanh thu theo SÂN CON
     */
    public function revenueByCourt(Request $request)
    {
        try {
            $ownerId = Auth::id();
            $ownerFacilityId = $this->getOwnerFacilityId($ownerId);

            if (!$ownerFacilityId) {
                Log::warning('No facility found for owner', ['owner_id' => $ownerId]);
                return response()->json(['labels' => [], 'revenues' => []]);
            }

            $range = $request->query('range', 'month');
            $courtId = $request->query('court', 'all');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if ($range === 'custom' && $startDate && $endDate) {
                $dateRange = ['start' => $startDate, 'end' => $endDate];
            } else {
                $dateRange = $this->getDateRange($range);
            }

            $query = DB::table('bookings')
                ->join('courts', function ($join) {
                    $join->on('bookings.court_id', '=', 'courts.court_id')
                        ->on('bookings.facility_id', '=', 'courts.facility_id');
                })
                ->where('bookings.facility_id', $ownerFacilityId)
                ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']]);

            if ($courtId !== 'all') {
                $query->where('bookings.court_id', $courtId);
            }

            $data = $query->select(
                'courts.court_name as label',
                DB::raw('SUM(bookings.unit_price) as revenue')
            )
                ->groupBy('courts.court_id', 'courts.court_name')
                ->having('revenue', '>', 0) // Chỉ lấy sân có doanh thu
                ->orderByDesc('revenue')
                ->get();

            Log::info('Revenue by court:', [
                'facility' => $ownerFacilityId,
                'date_range' => $dateRange,
                'count' => $data->count(),
                'data' => $data->toArray()
            ]);

            return response()->json([
                'labels' => $data->pluck('label')->toArray(),
                'revenues' => $data->pluck('revenue')->map(fn($v) => (float) $v)->toArray(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error in revenueByCourt', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'labels' => [],
                'revenues' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API trả dữ liệu so sánh nhiều sân (Multiple Courts Comparison)
     */
    public function courtsComparison(Request $request)
    {
        $ownerId = Auth::id();
        $ownerFacilityId = $this->getOwnerFacilityId($ownerId);
        if (!$ownerFacilityId)
            return response()->json([], 404);

        $range = $request->query('range', 'month');
        $courtId = $request->query('court', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($range === 'custom' && $startDate && $endDate) {
            $dateRange = ['start' => $startDate, 'end' => $endDate];
        } else {
            $dateRange = $this->getDateRange($range);
        }

        // Query đặt lẻ (từ invoices)
        $individualQuery = DB::table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->join('courts', function ($join) {
                $join->on('bookings.court_id', '=', 'courts.court_id')
                    ->on('bookings.facility_id', '=', 'courts.facility_id');
            })
            ->join('invoice_details', 'bookings.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.invoice_id')
            ->where('bookings.facility_id', $ownerFacilityId)
            ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']])
            ->where('bookings.status', 'like', '%thanh toán%');

        // Query hợp đồng (từ long_term_contracts)
        $contractQuery = DB::table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->join('courts', function ($join) {
                $join->on('bookings.court_id', '=', 'courts.court_id')
                    ->on('bookings.facility_id', '=', 'courts.facility_id');
            })
            ->join('long_term_contracts', 'bookings.invoice_detail_id', '=', 'long_term_contracts.invoice_detail_id')
            ->where('bookings.facility_id', $ownerFacilityId)
            ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']])
            ->where('long_term_contracts.payment_status', 'like', '%thanh toán%');

        if ($courtId !== 'all') {
            $individualQuery->where('bookings.court_id', $courtId);
            $contractQuery->where('bookings.court_id', $courtId);
        }

        // Lấy dữ liệu đặt lẻ
        $individualData = $individualQuery
            ->select('courts.court_name', 'facilities.facility_name', DB::raw('SUM(bookings.unit_price) as revenue'))
            ->groupBy('courts.court_id', 'courts.court_name', 'facilities.facility_id', 'facilities.facility_name')
            ->get()
            ->keyBy('court_name');

        // Lấy dữ liệu hợp đồng
        $contractData = $contractQuery
            ->select('courts.court_name', 'facilities.facility_name', DB::raw('SUM(bookings.unit_price) as revenue'))
            ->groupBy('courts.court_id', 'courts.court_name', 'facilities.facility_id', 'facilities.facility_name')
            ->get()
            ->keyBy('court_name');

        // Gộp tất cả tên sân
        $allCourtNames = $individualData->pluck('court_name')
            ->merge($contractData->pluck('court_name'))
            ->unique()
            ->sort()
            ->values();

        // Tạo response với dữ liệu tách biệt
        $result = [];
        foreach ($allCourtNames as $courtName) {
            $individualRevenue = (float) ($individualData[$courtName]->revenue ?? 0);
            $contractRevenue = (float) ($contractData[$courtName]->revenue ?? 0);
            $totalRevenue = $individualRevenue + $contractRevenue;

            if ($totalRevenue > 0) {
                $result[] = [
                    'court_name' => $courtName,
                    'individual_revenue' => $individualRevenue,
                    'contract_revenue' => $contractRevenue,
                    'revenue' => $totalRevenue
                ];
            }
        }

        // Sắp xếp theo tổng doanh thu giảm dần
        usort($result, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return response()->json(array_slice($result, 0, 10));
    }

    /**
     * API Top khách hàng
     */
    public function topCustomers(Request $request)
    {
        $ownerId = Auth::id();
        $ownerFacilityId = $this->getOwnerFacilityId($ownerId);
        if (!$ownerFacilityId)
            return response()->json([], 404);

        $range = $request->query('range', 'month');
        $courtId = $request->query('court', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($range === 'custom' && $startDate && $endDate) {
            $dateRange = ['start' => $startDate, 'end' => $endDate];
        } else {
            $dateRange = $this->getDateRange($range);
        }

        $query = DB::table('bookings')
            ->join('users', 'bookings.user_id', '=', 'users.user_id')
            ->where('bookings.facility_id', $ownerFacilityId)
            ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']]);

        if ($courtId !== 'all') {
            $query->where('bookings.court_id', $courtId);
        }

        $topCustomers = $query
        ->select(
            'users.user_id',
            'users.fullname',
            'users.phone',
            'users.email',
            DB::raw('COUNT(DISTINCT bookings.invoice_detail_id) as total_bookings'),
            DB::raw('SUM(bookings.unit_price) as total_spent')
        )
        ->groupBy('users.user_id', 'users.fullname', 'users.phone', 'users.email')
        ->orderBy('total_spent', 'desc')
        ->limit(10)
        ->get();

        return response()->json($topCustomers);
    }

    /**
     * Xuất PDF
     */
    public function exportPdf(Request $request)
    {
        $ownerId = Auth::id();
        $ownerFacilityId = $this->getOwnerFacilityId($ownerId);
        if (!$ownerFacilityId)
            return response("Không có cơ sở", 404);

        $range = $request->query('range', 'month');
        $courtId = $request->query('court', 'all');

        $dateRange = $this->getDateRange($range);
        $data = $this->calculateKPI($ownerId, $dateRange, $ownerFacilityId, $courtId);

        $selectedFacility = Facilities::find($ownerFacilityId);

        $pdf = Pdf::loadView('owner.report.pdf', ['data' => $data, 'dateRange' => $dateRange, 'facility' => $selectedFacility, 'range' => $range]);

        return $pdf->download('bao-cao-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Xuất Excel
     */
    public function exportExcel()
    {
        $bookings = Bookings::all();
        $export = new BookingsExport($bookings);
        return $export->export('Bao_cao_booking');
    }


    // ============ PRIVATE METHODS (Đã cập nhật) ============

    private function calculateKPI($ownerId, $dateRange, $facilityId, $courtId)
    {
        // --- QUERY BOOKING ĐẶT LẺ (từ invoices) ---
        $bookingIndividualQuery = DB::table('bookings')
        ->join('invoice_details', 'bookings.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
        ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.invoice_id')
        ->select('bookings.invoice_detail_id', 'invoices.final_amount as final_amount', 'bookings.court_id as court_id')
        ->distinct()
        ->where('bookings.facility_id', $facilityId)
        ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']]);
        
        // --- QUERY BOOKING HỢP ĐỒNG (từ long_term_contracts) ---
        $bookingContractQuery = DB::table('bookings')
        ->join('long_term_contracts', 'bookings.invoice_detail_id', '=', 'long_term_contracts.invoice_detail_id')
        ->select('bookings.invoice_detail_id', 'bookings.court_id as court_id')
        ->distinct()
        ->where('bookings.facility_id', $facilityId)
        ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']]);
        
        if ($courtId !== 'all') {
            $bookingIndividualQuery->where('bookings.court_id', $courtId);
            $bookingContractQuery->where('bookings.court_id', $courtId);

            $revenue = DB::table(function ($query) use ($facilityId, $dateRange, $courtId) {
                $query->select('invoice_details.invoice_detail_id', 'invoices.final_amount','bookings.booking_date','bookings.court_id')
                    ->distinct()
                    ->from('invoices')
                    ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
                    ->join('bookings', 'bookings.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
                    ->where('invoice_details.facility_id', $facilityId)
                    ->where('bookings.court_id', $courtId)
                    ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']])
                    ->where('invoices.payment_status', 'like', '%thanh toán%');
            }, 'distinct_invoices')
            ->sum('final_amount');
        }
        else
        {
            $revenue = DB::table(function ($query) use ($facilityId, $dateRange) {
                $query->select('invoice_details.invoice_detail_id', 'invoices.final_amount','bookings.booking_date')
                    ->distinct()
                    ->from('invoices')
                    ->join('invoice_details', 'invoices.invoice_id', '=', 'invoice_details.invoice_id')
                    ->join('bookings', 'bookings.invoice_detail_id', '=', 'invoice_details.invoice_detail_id')
                    ->where('invoice_details.facility_id', $facilityId)
                    ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']])
                    ->where('invoices.payment_status', 'like', '%thanh toán%');
            }, 'distinct_invoices')
            ->sum('final_amount');
        }

        $bookingIndividualQueryClone = clone $bookingIndividualQuery;
        $bookingContractQueryClone = clone $bookingContractQuery;
        $bookingsQueryClone2 = clone $bookingIndividualQuery;
        
        // Đếm từng loại
        $bookingsIndividual = $bookingIndividualQueryClone->distinct('bookings.invoice_detail_id')->count('bookings.invoice_detail_id');
        $bookingsContract = $bookingContractQueryClone->distinct('bookings.invoice_detail_id')->count('bookings.invoice_detail_id');
        $bookings = $bookingsIndividual + $bookingsContract; // Tổng cả 2 loại
        
        $customers = $bookingsQueryClone2->distinct('bookings.user_id')->count('bookings.user_id');
        $avgPrice = $bookings > 0 ? round($revenue / $bookings, 2) : 0;
        $utilization = $this->calculateUtilization($ownerId, $dateRange, $facilityId, $courtId);

        return [
            'revenue' => round($revenue, 2),
            'bookings' => $bookings,
            'bookings_individual' => $bookingsIndividual, // Đặt lẻ
            'bookings_contract' => $bookingsContract, // Hợp đồng
            'utilization' => round($utilization, 1),
            'customers' => $customers,
            'avgPrice' => $avgPrice
        ];
    }

    private function calculateUtilization($ownerId, $dateRange, $facilityId, $courtId)
    {
        $courtsQuery = DB::table('courts')
            ->join('facilities', 'courts.facility_id', '=', 'facilities.facility_id')
            ->where('facilities.owner_id', $ownerId)
            ->where('courts.facility_id', $facilityId); // Lọc theo ID cố định

        if ($courtId !== 'all') {
            $courtsQuery->where('courts.court_id', $courtId);
        }

        $totalCourts = $courtsQuery->count();
        if ($totalCourts == 0)
            return 0;

        $slotsPerDay = 38;
        $startDate = Carbon::parse($dateRange['start']);
        $endDate = Carbon::parse($dateRange['end']);
        $days = $startDate->diffInDays($endDate) + 1;
        $totalPossibleSlots = $totalCourts * $slotsPerDay * $days;

        $bookedSlotsQuery = DB::table('bookings')
            ->join('facilities', 'bookings.facility_id', '=', 'facilities.facility_id')
            ->where('facilities.owner_id', $ownerId)
            ->where('bookings.facility_id', $facilityId) // Lọc theo ID cố định
            ->whereBetween('bookings.booking_date', [$dateRange['start'], $dateRange['end']]);

        if ($courtId !== 'all') {
            $bookedSlotsQuery->where('bookings.court_id', $courtId);
        }

        $bookedSlots = $bookedSlotsQuery->count();
        return $totalPossibleSlots > 0 ? ($bookedSlots / $totalPossibleSlots) * 100 : 0;
    }

    // ... (Các hàm private còn lại giữ nguyên) ...
    private function getDateRange($range)
    {
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;

            case 'week':
                $start = $now->copy()->startOfWeek()->startOfDay();
                $end   = $now->copy()->endOfWeek()->endOfDay();
                break;

            case 'month':
                $start = $now->copy()->startOfMonth()->startOfDay();
                $end   = $now->copy()->endOfMonth()->endOfDay();
                break;

            case 'quarter':
                $start = $now->copy()->firstOfQuarter()->startOfDay();
                $end   = $now->copy()->lastOfQuarter()->endOfDay();
                break;

            case 'year':
                $start = $now->copy()->startOfYear()->startOfDay();
                $end   = $now->copy()->endOfYear()->endOfDay();
                break;

            default:
                // Nếu muốn, có thể mở rộng để hỗ trợ custom range
                $start = $now->copy()->startOfMonth()->startOfDay();
                $end   = $now->copy()->endOfMonth()->endOfDay();
        }

        return ['start' => $start, 'end' => $end];
    }

    private function getPreviousDateRange($range)
    {
        $now = Carbon::now();
        switch ($range) {
            case 'today':
                $prev = $now->copy()->subDay();
                return ['start' => $prev->startOfDay()->toDateString(), 'end' => $prev->endOfDay()->toDateString()];
            case 'week':
                $prev = $now->copy()->subWeek();
                return ['start' => $prev->startOfWeek()->toDateString(), 'end' => $prev->endOfWeek()->toDateString()];
            case 'month':
                $prev = $now->copy()->subMonth();
                return ['start' => $prev->startOfMonth()->toDateString(), 'end' => $prev->endOfMonth()->toDateString()];
            case 'quarter':
                $prev = $now->copy()->subQuarter();
                return ['start' => $prev->firstOfQuarter()->toDateString(), 'end' => $prev->lastOfQuarter()->toDateString()];
            case 'year':
                $prev = $now->copy()->subYear();
                return ['start' => $prev->startOfYear()->toDateString(), 'end' => $prev->endOfYear()->toDateString()];
            default:
                $prev = $now->copy()->subMonth();
                return ['start' => $prev->startOfMonth()->toDateString(), 'end' => $prev->endOfMonth()->toDateString()];
        }
    }

    private function getCustomPreviousRange($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = $start->diffInDays($end);

        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($days);

        return [
            'start' => $prevStart->toDateString(),
            'end' => $prevEnd->toDateString()
        ];
    }

    private function calculateChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}