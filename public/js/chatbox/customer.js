document.addEventListener("DOMContentLoaded", () => {
  let currentMaDN = null;
  const socket = io("http://localhost:3000", {
    transports: ["websocket", "polling"]
  });

  const maKH = document.getElementById("maKH")?.value;
  socket.emit("customer_online", maKH); // Đảm bảo emit sau khi socket được khởi tạo

  // Nhận phản hồi từ doanh nghiệp
  socket.on("receive_reply", ({ reply }) => {
    console.log("Phản hồi từ doanh nghiệp:", reply);
    appendMessage(`Doanh nghiệp: ${reply}`);
  });

  // Load danh sách doanh nghiệp online
  fetch("http://localhost:3000/api/doanhnghiep/online")
    .then((response) => response.json())
    .then((data) => {
      const select = document.getElementById("companySelect");
      select.innerHTML = ""; //mặc định "Loading..."

      if (data.length === 0) {
        const option = document.createElement("option");
        option.textContent = "Không có doanh nghiệp online";
        option.disabled = true;
        option.selected = true;
        select.appendChild(option);
        return;
      }

      data.forEach(({ maDN, tenDN }) => {
        const option = document.createElement("option");
        option.value = maDN;
        option.textContent = tenDN;
        select.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Lỗi khi tải doanh nghiệp online:", error);
    });

  // Gửi yêu cầu chat
  const form = document.getElementById("chatRequestForm");
  if (!form) {
    console.error("Không tìm thấy form chatRequestForm");
    return;
  }

  form.addEventListener("submit", function(e) {
    e.preventDefault(); // Ngăn reload trang

    const maDN = document.getElementById("companySelect").value;
    const maKH = form.querySelector("input[name='maKH']").value;
    const message = form.querySelector("textarea[name='message']").value;

    currentMaDN = maDN;

    // Gửi yêu cầu chat tới Socket.IO server
    socket.emit("chat_request", { maKH, maDN, message });

    // Hiển thị nội dung chat ra màn hình
    appendMessage(`Bạn: ${message}`);
    form.reset(); // Xoá nội dung sau khi gửi
  });

  function appendMessage(msg) {
    const box = document.getElementById("messages") || createBox();
    const div = document.createElement("div");
    div.textContent = msg;
    box.appendChild(div);
  }

  function createBox() {
    const box = document.createElement("div");
    box.id = "messages";
    document.querySelector(".chat-box").appendChild(box);
    return box;
  }
});
