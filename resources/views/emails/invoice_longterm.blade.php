<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn - DreamSports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #0db2ac, #089c66);
            color: #fff;
            text-align: center;
            padding: 35px 25px;
        }
        .header h1 {
            font-size: 30px;
            margin: 0;
            font-weight: 800;
        }
        .content {
            padding: 35px 40px;
        }
        .content h2 {
            color: #333;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .content p {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
        }
        .invoice-box {
            margin-top: 25px;
            border-left: 4px solid #0db2ac;
            padding: 15px 20px;
            background: #f8fdfc;
            border-radius: 10px;
        }
        .invoice-box p {
            margin: 6px 0;
            font-size: 15px;
        }
        .cta-btn {
            display: inline-block;
            background: #0db27f;
            color: white !important;
            padding: 12px 22px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            text-align: center;
        }
        .footer {
            font-size: 13px;
            color: #777;
            text-align: center;
            padding: 20px;
            margin-top: 10px;
        }
        .logo {
            max-height: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="email-wrapper">

    <!-- Header -->
    <div class="header">
        <h1>DreamSports</h1>
        <p style="margin-top:5px;font-size:15px;">Đặt sân cầu lông – Nhanh chóng & Chuyên nghiệp</p>
    </div>

    <!-- Nội dung chính -->
    <div class="content">
        <h2>Xin chào {{ $customerName ?? 'Quý khách' }},</h2>

        <p>Cảm ơn bạn đã sử dụng dịch vụ của <strong>DreamSports</strong>!  
            Hóa đơn đặt sân của bạn đã được tạo và đính kèm trong file PDF.</p>

        {{-- <div class="invoice-box">
            <p><strong>Mã hoá đơn:</strong> {{ $invoiceCode ?? 'HD123456' }}</p>
            <p><strong>Ngày thanh toán:</strong> {{ $paymentDate ?? '2025-11-16' }}</p>
            <p><strong>Sân:</strong> {{ $courtName ?? 'Sân số 3 - DreamSports' }}</p>
            <p><strong>Khung giờ:</strong> {{ $timeRange ?? '18:00 - 20:00' }}</p>
            <p><strong>Tổng tiền:</strong> <span style="color:#0db27f;font-weight:700;">
                {{ $totalPrice ?? '120,000đ' }}
            </span></p>
        </div>

        <a href="{{ $orderLink ?? '#' }}" class="cta-btn">Xem Chi Tiết Đơn Hàng</a> --}}

        <p style="margin-top:25px;">Nếu có bất kỳ thắc mắc nào, bạn có thể phản hồi trực tiếp email này hoặc truy cập website của chúng tôi.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        Cảm ơn bạn đã chọn DreamSports ❤️ <br>
        Website: sancaulong.app – Hotline hỗ trợ: 0346021604
    </div>

</div>

</body>
</html>
