const express = require("express");
const http = require("http");
const { Server } = require("socket.io");
const mysql = require("mysql2/promise");
const cors = require("cors");

const app = express();
const server = http.createServer(app);
const corsOptions = {
  origin: "*",
  credentials: true,
};
app.use(cors(corsOptions));

const io = new Server(server, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    credentials: true
  }
});

// Kết nối CSDL
const db = mysql.createPool({
  host: "localhost",
  user: "cnm",
  password: "123",
  database: "sancaulong"
});

// Doanh Nghiệp
let onlineBusinesses = new Map();
let onlineCustomers = new Map();

const getTenDN = async (maDN) => {
  try {
    const [rows] = await db.execute("SELECT tenDN FROM doanhnghiep WHERE maDN = ?", [maDN]);
    return rows[0]?.tenDN || "Không xác định";
  } catch (error) {
    console.error("Lỗi khi truy vấn tên doanh nghiệp:", error);
    return "Lỗi truy vấn";
  }
};

app.get("/api/doanhnghiep/online", async (req, res) => {
  try {
    const maDNs = [...new Set(onlineBusinesses.values())];
    console.log("API gọi danh sách doanh nghiệp online:", maDNs);

    const businesses = await Promise.all(maDNs.map(async (maDN) => {
      const tenDN = await getTenDN(maDN);
      return { maDN, tenDN };
    }));


    console.log("Danh sách doanh nghiệp trả về:", businesses);
    res.json(businesses);
  } catch (error) {
    console.error("Lỗi khi lấy danh sách doanh nghiệp online:", error);
    res.status(500).json({ error: "Lỗi khi lấy danh sách doanh nghiệp online" });
  }
});


io.on("connection", (socket) => {
   console.log("Client connected:", socket.id);

  socket.on("business_online", (maDN) => {
    console.log("Doanh nghiệp online:", maDN, "socket.id:", socket.id);
    if (maDN) {
      onlineBusinesses.set(socket.id, maDN);
      console.log("Tất cả doanh nghiệp online hiện tại:", Array.from(onlineBusinesses.entries()));
    }
  });

    socket.on("customer_online", (maKH) => {
      if (maKH) {
        onlineCustomers.set(maKH, socket.id);
        console.log("Khách hàng online:", maKH, "socket.id:", socket.id);
      }
    });

  socket.on("chat_request", ({ maKH, maDN, message }) => {
    const businessSocketId = [...onlineBusinesses.entries()].find(([_, value]) => value === maDN)?.[0];

    if (businessSocketId) {
      db.execute("SELECT tenKH FROM khachhang WHERE maKH = ?", [maKH])
        .then(([rows]) => {
          const tenKH = rows?.[0]?.tenKH || "Khách hàng";
          io.to(businessSocketId).emit("new_chat_request", { maKH, tenKH, message });
        })
        .catch((err) => {
          console.error("Lỗi truy vấn khách hàng:", err);
        });
    }
  });

  socket.on("business_reply", ({ maKH, reply }) => {
    const customerSocketId = onlineCustomers.get(maKH);
    if (customerSocketId) {
      io.to(customerSocketId).emit("receive_reply", { reply });
    } else {
      console.warn("Không tìm thấy socket của khách hàng", maKH);
    }
  });

  socket.on("disconnect", () => {
    onlineBusinesses.delete(socket.id);
   for (const [maKH, sid] of onlineCustomers.entries()) {
      if (sid === socket.id) {
        onlineCustomers.delete(maKH);
        break;
      }
    }
    console.log("Client disconnected:", socket.id);
  });
});

server.listen(3000, () => {
  console.log("Server running on http://localhost:3000");
});

app.use(express.static("public"));

// -----------------------------------------------------------
