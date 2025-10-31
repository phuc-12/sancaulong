<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đang chuyển hướng...</title>
</head>
<body style="display:flex; align-items:center; justify-content:center; height:100vh; flex-direction:column; font-family:sans-serif;">
    <p>Vui lòng chờ trong giây lát...</p>

    <form id="redirectForm" method="POST" action="{{ route('payment_contract') }}">
        @csrf
        <input type="hidden" name="summary" value='@json($summary)'>
        <input type="hidden" name="details" value='@json($details)'>
        <input type="hidden" name="lines" value='@json($lines)'>
        <input type="hidden" name="userInfo" value='@json($userInfo)'>
    </form>
    <script>
        // Tự động gửi form sau 1s
        setTimeout(() => {
            document.getElementById('redirectForm').submit();
        }, 1000);
    </script>
</body>
</html>
