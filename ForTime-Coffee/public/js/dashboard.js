/**
 * DASHBOARD JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Vẽ biểu đồ (Chart.js) doanh thu, nguồn thu và xử lý giao diện Dashboard.
 * - View sử dụng: app/views/admin/dashboard/index.php
 * - Controller kết nối: Dashboard.php
 * - Model liên quan: DashboardModel.php, OrderModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function () {
    
    // -------------------------------------------------------
    // 1. XỬ LÝ TOGGLE SIDEBAR
    // -------------------------------------------------------
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }

    // -------------------------------------------------------
    // 2. VẼ BIỂU ĐỒ DOANH THU (LINE CHART)
    // -------------------------------------------------------
    const revenueCanvas = document.getElementById('revenueChart');
    
    if (revenueCanvas) {
        const ctx = revenueCanvas.getContext('2d');
        
        // Lấy dữ liệu từ thẻ input hidden
        const labelsInput = document.getElementById('chartLabels');
        const valuesInput = document.getElementById('chartValues');
        
        let chartLabels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']; // Mặc định
        let chartData = [0, 0, 0, 0, 0, 0, 0]; // Mặc định

        // Parse JSON an toàn
        if (labelsInput && valuesInput) {
            try {
                if(labelsInput.value) chartLabels = JSON.parse(labelsInput.value);
                if(valuesInput.value) chartData = JSON.parse(valuesInput.value);
            } catch (e) {
                console.error("Lỗi parse dữ liệu biểu đồ doanh thu:", e);
            }
        }

        // Cấu hình biểu đồ
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: chartData,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4e73df',
                    pointHoverBackgroundColor: '#4e73df',
                    pointHoverBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleColor: '#6e707e',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed.y;
                                return 'Doanh thu: ' + value.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4],
                            color: "#eaecf4",
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10,
                            callback: function(value) {
                                if(value >= 1000000) return (value/1000000).toFixed(1) + 'M';
                                if(value >= 1000) return (value/1000).toFixed(0) + 'k';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { padding: 10 }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------
    // 3. VẼ BIỂU ĐỒ TRÒN (DOUGHNUT CHART)
    // -------------------------------------------------------
    const sourceCanvas = document.getElementById('sourceChart');
    const topProductInput = document.getElementById('topProductsData');

    if (sourceCanvas && topProductInput) {
        let labels = [];
        let dataRevenue = [];
        
        try {
            if (topProductInput.value) {
                const rawData = JSON.parse(topProductInput.value);
                if (Array.isArray(rawData) && rawData.length > 0) {
                    labels = rawData.map(item => item.product_name);
                    dataRevenue = rawData.map(item => item.revenue);
                }
            }
        } catch (e) {
            console.error("Lỗi parse dữ liệu biểu đồ tròn:", e);
        }

        // Dữ liệu mặc định nếu trống
        if (labels.length === 0) {
            labels = ["Chưa có dữ liệu"];
            dataRevenue = [1]; // 1 phần màu xám
        }

        const backgroundColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];

        const ctxSource = sourceCanvas.getContext('2d');
        new Chart(ctxSource, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataRevenue,
                    backgroundColor: backgroundColors,
                    hoverBackgroundColor: backgroundColors,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed;
                                if (label) { label += ': '; }
                                label += value.toLocaleString('vi-VN') + 'đ';
                                return label;
                            }
                        }
                    }
                },
                cutout: '70%',
            },
        });
    }
});