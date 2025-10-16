$(document).ready(function() {
    function ktUsername() {
        let username = $("#txtUsername").val().trim();
        let usernameRegex = /^[A-Za-z][A-Za-z0-9]*$/;
        if (username == "") {
            $("#errUsername").html("Username không được để trống.");
            return false;
        } else if(!usernameRegex.test(username))
        {
            $("#errUsername").html("Username không đúng định dạng.");
            return false;
        }
        else {
            $("#errUsername").html("");
            return true;
        }
    }
    $("#txtUsername").blur(function() {
        ktUsername();
    });
    function ktEmail() {
        let email = $("#txtEmail").val().trim();
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === "") {
            $("#errEmail").html("Email không được để trống.");
            return false;
        } else if (!emailRegex.test(email)) {
            $("#errEmail").html("Email không đúng định dạng.");
            return false;
        } else {
            $("#errEmail").html("");
            return true;
        }
    }

    function ktSDT() {
        let sdt = $("#txtSDT").val().trim();
        let phoneRegex = /^(03|09|08|07)[0-9]{8}$/;
        if (sdt === "") {
            $("#errSDT").html("Số điện thoại không được để trống.");
            return false;
        } else if (!phoneRegex.test(sdt)) {
            $("#errSDT").html("Số điện thoại không hợp lệ.");
            return false;
        } else {
            $("#errSDT").html("");
            return true;
        }
    }

    function ktPassword() {
        let pw = $("#txtPassword").val();
        if (pw === "") {
            $("#errPW").html("Mật khẩu không được để trống.");
            return false;
        } else if (pw.length < 6) {
            $("#errPW").html("Mật khẩu phải có ít nhất 6 ký tự.");
            return false;
        } else {
            $("#errPW").html("");
            return true;
        }
    }

    function ktRePassword() {
        let pw = $("#txtPassword").val();
        let repw = $("#txt-rePassword").val();
        if (repw === "") {
            $("#errRePW").html("Vui lòng xác nhận mật khẩu.");
            return false;
        } else if (pw !== repw) {
            $("#errRePW").html("Mật khẩu xác nhận không khớp.");
            return false;
        } else {
            $("#errRePW").html("");
            return true;
        }
    }

    // Gán sự kiện blur cho các input
    
    $("#txtEmail").blur(ktEmail);
    $("#txtSDT").blur(ktSDT);
    $("#txtPassword").blur(ktPassword);
    $("#txt-rePassword").blur(ktRePassword);

    function register() {
        var username = document.getElementById('txtUsername').value;
        var email = document.getElementById('txtEmail').value;
        var soDienThoai = document.getElementById('txtPhone').value;
        var pw = document.getElementById('txtPassword').value;
        var rePW = document.getElementById('txt-rePassword').value;
    
        // Lưu thông tin đăng ký vào local storage
        localStorage.setItem('username', username);
        localStorage.setItem('passwords', pw);
        localStorage.setItem('email', email);
        localStorage.setItem('sodienthoai', soDienThoai);
        
    
        alert("Đăng ký thành công!");
        window.location.href = "login.php";
        return false;
    }
});