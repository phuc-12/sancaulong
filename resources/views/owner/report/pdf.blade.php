<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4472C4;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4472C4;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .kpi-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            background: white;
        }
        .kpi-card.revenue { border-color: #ff8c42; background: #fff3e6; }
        .kpi-card.bookings { border-color: #4a90e2; background: #e6f2ff; }
        .kpi-card.utilization { border-color: #ffd93d; background: #fffbea; }
        .kpi-card.customers { border-color: #9b59b6; background: #f3e6ff; }
        .kpi-card.avgPrice { border-color: #2ecc71; background: #e6fff2; }
        .kpi-card.growth { border-color: #e91e63; background: #ffe6f2; }
        .kpi-label {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
        }
        .kpi-value {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            color: #999;
            font-size: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .summary-table th {
            background: #4472C4;
            color: white;
            font-weight: bold;
        }
        .summary-table tr:nth-child(even) {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>BÁO CÁO DASHBOARD</h1>
        <p>Tổng quan hoạt động kinh doanh sân cầu lông</p>
    </div>

    <!-- Thông tin báo cáo -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Thời gian:</span>
            <span>{{ \Carbon\Carbon::parse($dateRange['start'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateRange['end'])->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cơ sở:</span>
            <span>{{ $facility ? $facility->facility_name : 'Tất cả cơ sở' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Ngày xuất báo cáo:</span>
            <span>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card revenue">
            <div class="kpi-label">Tổng Doanh Thu</div>
            <div class="kpi-value">{{ number_format($data['revenue'], 0, ',', '.') }} ₫</div>
        </div>
        
        <div class="kpi-card bookings">
            <div class="kpi-label">Tổng Đặt Sân</div>
            <div class="kpi-value">{{ number_format($data['bookings'], 0, ',', '.') }}</div>
        </div>
        
        <div class="kpi-card utilization">
            <div class="kpi-label">Công Suất</div>
            <div class="kpi-value">{{ number_format($data['utilization'], 1) }}%</div>
        </div>
        
        <div class="kpi-card customers">
            <div class="kpi-label">Khách Hàng</div>
            <div class="kpi-value">{{ number_format($data['customers'], 0, ',', '.') }}</div>
        </div>
        
        <div class="kpi-card avgPrice">
            <div class="kpi-label">Giá Trung Bình</div>
            <div class="kpi-value">{{ number_format($data['avgPrice'], 0, ',', '.') }} ₫</div>
        </div>
        
        <div class="kpi-card growth">
            <div class="kpi-label">Tăng Trưởng</div>
            <div class="kpi-value">-</div>
        </div>
    </div>

    <!-- Summary Table -->
    <table class="summary-table">
        <thead>
            <tr>
                <th>Chỉ số</th>
                <th>Giá trị</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Tổng doanh thu</strong></td>
                <td>{{ number_format($data['revenue'], 0, ',', '.') }} ₫</td>
                <td>Tổng tiền từ các booking trong kỳ</td>
            </tr>
            <tr>
                <td><strong>Tổng số booking</strong></td>
                <td>{{ number_format($data['bookings'], 0, ',', '.') }} lượt</td>
                <td>Số lượt đặt sân trong kỳ</td>
            </tr>
            <tr>
                <td><strong>Công suất sử dụng</strong></td>
                <td>{{ number_format($data['utilization'], 1) }}%</td>
                <td>Tỷ lệ sân được sử dụng / tổng khả năng</td>
            </tr>
            <tr>
                <td><strong>Số khách hàng</strong></td>
                <td>{{ number_format($data['customers'], 0, ',', '.') }} người</td>
                <td>Khách hàng duy nhất đã đặt sân</td>
            </tr>
            <tr>
                <td><strong>Giá trung bình</strong></td>
                <td>{{ number_format($data['avgPrice'], 0, ',', '.') }} ₫</td>
                <td>Giá trung bình mỗi lần booking</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>© {{ date('Y') }} - Hệ thống quản lý sân cầu lông</p>
        <p>Báo cáo được tạo tự động từ hệ thống</p>
    </div>
</body>
</html>