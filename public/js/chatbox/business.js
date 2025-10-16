document.addEventListener("DOMContentLoaded", () => {
  window.currentMaKH = null;
  const socket = io("http://localhost:3000", {
    transports: ["websocket", "polling"]
  });

  const maDN = document.getElementById("maDN")?.value || localStorage.getItem("maDN");
  if (maDN) {
    localStorage.setItem("maDN", maDN);
    socket.emit("business_online", maDN);
  }

  socket.on("new_chat_request", ({ maKH, tenKH, message }) => {
    window.currentMaKH = maKH;
    console.log("Đã set window.currentMaKH =", window.currentMaKH);

    const item = document.createElement("li");
    item.textContent = `${tenKH}: ${message}`;
    document.getElementById("requests").appendChild(item);
  });

  const form = document.getElementById("chatForm");

  // Xoá hết event submit cũ (nếu có) để tránh gán nhiều lần
  form.replaceWith(form.cloneNode(true)); 
  const newForm = document.getElementById("chatForm");

  newForm.addEventListener("submit", function (e) {
    e.preventDefault();
    console.log("Submit hiện tại window.currentMaKH =", window.currentMaKH);
    const replyTextInput = document.getElementById("replyText");
    const replyText = replyTextInput.value.trim();

    console.log("replyText lúc submit:", JSON.stringify(replyText));
    console.log("currentMaKH =", window.currentMaKH);

    if (!replyText) {
      alert("Nội dung phản hồi không được để trống!");
      return;
    }

    if (!window.currentMaKH) {
      alert("Chưa có khách hàng để trả lời!");
      return;
    }

    socket.emit("business_reply", {
      maKH: window.currentMaKH,
      reply: replyText
    });

    const item = document.createElement("li");
    item.textContent = `Bạn: ${replyText}`;
    document.getElementById("requests").appendChild(item);

    replyTextInput.value = "";
  });
});
