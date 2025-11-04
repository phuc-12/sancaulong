
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang chuyển hướng...</title>
</head>
<body style="display:flex; align-items:center; justify-content:center; height:100vh; flex-direction:column; font-family:sans-serif;">
    <p>Vui lòng chờ trong giây lát...</p>

    <form id="redirectMybookings" method="POST" action="{{ route('lich_dat_san') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user_id }}">
        <input type="hidden" name="success_message" value="{{ $success_message }}">
    </form>

    <script>
        // Tự động gửi form sau 1s
        setTimeout(() => {
            document.getElementById('redirectMybookings').submit();
        }, 1000);
    </script>
</body>
</html>
