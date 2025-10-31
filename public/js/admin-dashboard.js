// public/js/admin-dashboard.js

document.addEventListener('DOMContentLoaded', () => {

    // ===========================================
    // BIỂU ĐỒ DONUT
    // ===========================================

    // ===========================================
    // BIỂU ĐỒ DOANH THU (LINE CHART)
    // ===========================================
    // Lấy phần tử canvas
    const revenueChartElement = document.getElementById('revenueChart');

    // Kiểm tra xem canvas có tồn tại không
    if (revenueChartElement) {
        // Lấy URL từ thuộc tính 'data-url'
        const revenueChartUrl = revenueChartElement.dataset.url;
        const ctxRevenue = revenueChartElement.getContext('2d');
        let revenueChartInstance; // Biến để lưu trữ biểu đồ

        // 1. Cấu hình biểu đồ
        const chartConfig = {
            type: 'line',
            data: {
                labels: [], // Sẽ được điền bởi API
                datasets: [{
                    label: "Doanh thu",
                    data: [], // Sẽ được điền bởi API
                    backgroundColor: 'rgba(25, 135, 84, 0.05)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value.toLocaleString('vi-VN');
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toLocaleString('vi-VN');
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        };

        // 2. Hàm gọi API và cập nhật biểu đồ
        async function fetchRevenueData(filter, title) {
            try {
                
                // Thay thế {{ route(...) }} bằng biến revenueChartUrl
                const response = await fetch(`${revenueChartUrl}?filter=${filter}`);

                if (!response.ok) throw new Error('Network response was not ok');

                const result = await response.json();

                // Cập nhật dữ liệu cho biểu đồ
                chartConfig.data.labels = result.labels;
                chartConfig.data.datasets[0].data = result.data;

                // Cập nhật tiêu đề
                document.getElementById('revenueChartTitle').innerText = title;

                // Hủy biểu đồ cũ (nếu có) và vẽ lại
                if (revenueChartInstance) {
                    revenueChartInstance.destroy();
                }
                revenueChartInstance = new Chart(ctxRevenue, chartConfig);

            } catch (error) {
                console.error('Lỗi khi lấy dữ liệu biểu đồ:', error);
            }
        }

        // 3. Lắng nghe sự kiện click trên các nút filter
        document.querySelectorAll('.chart-filter').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();

                const filter = this.dataset.filter;
                const title = this.dataset.title;

                // Gọi hàm để tải dữ liệu mới
                fetchRevenueData(filter, title);
            });
        });

        // 4. Tải dữ liệu lần đầu tiên (mặc định 30 ngày)
        fetchRevenueData('30days', 'Tổng Quan Doanh Thu (30 Ngày)');
    } // Đóng thẻ "if (revenueChartElement)"

});