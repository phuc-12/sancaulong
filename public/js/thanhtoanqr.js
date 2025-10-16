// let courses = 
// [
//     {
//         courseID: "Thanh toán tiền đặt sân",
//         courseName: "Marketing",
//         coursePrice: 10000,
//     },
// ];

let MY_BANK = {
    BANK_ID: "VCB",
    ACCOUNT_NO: "1025153831"
}

document.addEventListener("DOMContentLoaded", () => {
    // const courseInner = document.querySelector(".courses_inner");
    // let coursesRenderUI = "";
    // courses.forEach((item, index) => {
    //     coursesRenderUI += `
    //         <div class="courses_item" style="float:left; margin: 20px; background-color: green; padding: 20px; border-radius: 20px;">
    //             <img src="" alt="">
    //             <p>${item.courseName}</p>
    //             <p>${item.coursePrice}</p>
    //             <a class="course_item_btn">Mua</a>
    //         </div>
    //     `;
    // });
    // courseInner.innerHTML = coursesRenderUI;

    const btns = document.querySelectorAll(".course_item_btn");
    const paid_content = document.getElementById("paid_content");
    const paid_price = document.getElementById("paid_price");
    const course_qr_img = document.querySelector(".course_qr_img");
    btns.forEach((item,index) => {
        item.addEventListener("click", () => {
            const paidContent = `Thanh Toán Tổng Tiền Sân`;
            // const paidPrice = document.getElementById();
            var paidPrice = parseInt(document.getElementById("tongtien").value);
            let QR = `https://img.vietqr.io/image/${MY_BANK.BANK_ID}-${MY_BANK.ACCOUNT_NO}-compact2.png?amount=${paidPrice}&addInfo=${paidContent}`;
            course_qr_img.src = QR;
            
            // paid_content.innerHTML = paidContent;
            // paid_price.innerHTML = paidPrice;
            
            setTimeout(() => {
                    setInterval(() => {
                        checkPaid(paidPrice);
                }, 2000);
            }, 5000);
        });
    });
});
let isSucess = false;
async function checkPaid(price) {
    if(isSucess)
    {
        var maKH = document.getElementById("maKH").value;
        window.location="pay-complete.php?maKH="+maKH;
        // window.location="user-complete.php?maKH="+maKH;
    }
    else 
    {
        try {
            const response = await fetch(
                "https://script.google.com/macros/s/AKfycbxUquYh2WxhPZPyCjSY4nyKlaqlnaiYgdD8Gkq4vtnD0iIzURn4WQmOoY9faYTMFyF4/exec"
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