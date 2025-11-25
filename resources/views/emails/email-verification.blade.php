<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0BAE79, #064A43);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 20px 0;
        }
        .btn-verify {
            display: inline-block;
            background: linear-gradient(135deg, #0BAE79, #064A43);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(11, 174, 121, 0.3);
            transition: all 0.3s;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(11, 174, 121, 0.4);
        }
        .info-box {
            background: #e8f5f1;
            padding: 15px;
            border-left: 4px solid #0BAE79;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning {
            color: #d9534f;
            font-size: 13px;
            margin-top: 20px;
            padding: 15px;
            background: #fff5f5;
            border-left: 4px solid #d9534f;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .link-text {
            word-break: break-all;
            color: #666;
            font-size: 12px;
            background: #f8f8f8;
            padding: 10px;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">✉️</div>
            <h2>Xác thực tài khoản</h2>
        </div>
        
        <div class="content">
            <p>Xin chào <strong>{{ $userName }}</strong>,</p>
            
            <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>DreamSports</strong>! 🎉</p>
            
            <p>Để hoàn tất quá trình đăng ký và bắt đầu sử dụng các dịch vụ của chúng tôi, vui lòng xác thực địa chỉ email của bạn bằng cách nhấp vào nút bên dưới:</p>
            
            <div style="text-align: center; margin: 30px 0; ">
                <a href="{{ $verificationUrl }}" class="btn-verify" style="color: white">
                    ✓ Xác thực email của tôi
                </a>
            </div>
            
            <div class="info-box">
                <p style="margin: 0;"><strong>📌 Lưu ý:</strong></p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Link xác thực có hiệu lực trong <strong>60 phút</strong></li>
                    <li>Sau khi xác thực, bạn có thể đăng nhập và sử dụng đầy đủ tính năng</li>
                </ul>
            </div>
            
            <p>Nếu nút bên trên không hoạt động, bạn có thể copy và dán link sau vào trình duyệt:</p>
            <div class="link-text">
                {{ $verificationUrl }}
            </div>
            
            <div class="warning">
                <p style="margin: 0;"><strong>⚠️ Quan trọng:</strong></p>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Nếu bạn không tạo tài khoản này, vui lòng bỏ qua email này</li>
                    <li>Không chia sẻ link xác thực với bất kỳ ai</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>Trân trọng,<br><strong>Đội ngũ DreamSports</strong></p>
            <p style="font-size: 12px; color: #999;">Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>