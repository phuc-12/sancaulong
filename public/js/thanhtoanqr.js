
let MY_BANK = {
    BANK_ID: "VCB",
    ACCOUNT_NO: "1025153831"
}

document.addEventListener("DOMContentLoaded", () => {

    const btns = document.querySelectorAll(".course_item_btn");
    const paid_content = document.getElementById("paid_content");
    const paid_price = document.getElementById("paid_price");
    const course_qr_img = document.querySelector(".course_qr_img");
    btns.forEach((item,index) => {
        item.addEventListener("click", () => {
            const paidContent = `Thanh Toán Tổng Tiền Sân`;
            var paidPrice = parseInt(document.getElementById("tongtien").value);
            let QR = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-compact2.png?amount=${paidPrice}&addInfo=${paidContent}`;
            course_qr_img.src = QR;
            
            // paid_content.innerHTML = paidContent;
            // paid_price.innerHTML = paidPrice;
            
            setTimeout(() => {
                    setInterval(() => {
                        checkPaid(paidPrice);
                }, 10000);
            }, 1000);
        });
    });
});
let isSucess = false;
async function checkPaid(price) {
    if (isSucess) {
        console.log("Thanh toán thành công — gửi dữ liệu lên server...");
        document.getElementById("paymentCompleteForm").submit();
    }
    else 
    {
        try {
            const response = await fetch(
                "https://script.google.com/macros/s/AKfycbwIKNqvZftMggqULAy8J-rPGwEsw1HVvJbJK5jfKkNJJ-EMf6km5_xJibYyLs04wM0xFQ/exec"
            );
            const data = await response.json();
            const lastPaid = data.data[data.data.length - 1];
            lastPrice = lastPaid["Giá trị"];
            if(lastPrice >= price) {
                alert("Thanh toán thành công" + " " + lastPrice + "VNĐ");
                isSucess = true;
            } 
            else 
            {
                alert("Không thành công hoặc không đủ tiền");
            }

        } catch {
            console.error("Lỗi");
        }
    }
}