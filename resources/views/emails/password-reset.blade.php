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
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .code-box {
            background: #f4f4f4;
            border: 2px dashed #0BAE79;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
            border-radius: 8px;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #0BAE79;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .info {
            background: #e8f5f1;
            padding: 15px;
            border-left: 4px solid #0BAE79;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning {
            color: #d9534f;
            font-size: 14px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔐 Đặt lại mật khẩu</h2>
        </div>
        
        <p>Xin chào,</p>
        
        <p>Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn trên <strong>DreamSports</strong>.</p>
        
        <div class="code-box">
            <p style="margin: 0 0 10px 0; color: #666;">Mã xác nhận của bạn là:</p>
            <div class="code">{{ $code }}</div>
        </div>
        
        <div class="info">
            <p style="margin: 0;"><strong>⏰ Thời gian hiệu lực:</strong> Mã này sẽ hết hạn sau <strong>{{ $expiresInMinutes }} phút</strong>.</p>
        </div>
        
        <p>Vui lòng nhập mã này vào trang đặt lại mật khẩu để tiếp tục.</p>
        
        <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn.</p>
        
        <div class="warning">
            <p style="margin: 0;"><strong>⚠️ Lưu ý bảo mật:</strong></p>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Không chia sẻ mã này với bất kỳ ai</li>
                <li>DreamSports sẽ không bao giờ yêu cầu mã xác nhận qua điện thoại</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>Trân trọng,<br><strong>Đội ngũ DreamSports</strong></p>
            <p style="font-size: 12px; color: #999;">Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>