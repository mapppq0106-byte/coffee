/**
 * STATS REPORT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Xuất báo cáo hiệu suất (KPI) chi tiết của nhân viên ra Excel.
 * - View sử dụng: app/views/admin/users/stats_single.php
 * - Controller kết nối: StaffController.php (Method: stats)
 * - Model liên quan: UserModel.php (Dữ liệu chấm công & doanh số)
 * -------------------------------------------------------------------------
 */

// ============================================================
// 1. HELPER FUNCTIONS (Hàm hỗ trợ)
// ============================================================

/**
 * Lấy nội dung text từ element theo ID (An toàn)
 */
const getText = (id) => {
    const el = document.getElementById(id);
    return el ? el.innerText.trim() : '';
};

/**
 * Lấy giá trị value từ input theo ID (An toàn)
 */
const getValue = (id) => {
    const el = document.getElementById(id);
    return el ? el.value.trim() : '';
};

// ============================================================
// 2. MAIN FUNCTION (Gọi từ nút bấm)
// ============================================================

function exportKpiExcel() {
    // Kiểm tra thư viện XLSX đã được load chưa
    if (typeof XLSX === 'undefined') {
        alert('Thư viện xuất Excel chưa sẵn sàng. Vui lòng tải lại trang!');
        return;
    }

    // A. Thu thập dữ liệu Tổng quan
    const name = getText('staffName');
    const from = getValue('dateFrom');
    const to = getValue('dateTo');
    
    const revenue = getText('valRevenue');
    const orders = getText('valOrders');
    const hours = getText('valHours');

    // B. Cấu trúc dữ liệu Excel (Header & Tổng quan)
    const data = [
        ["BÁO CÁO HIỆU SUẤT NHÂN VIÊN"],
        ["Nhân viên:", name],
        ["Kỳ báo cáo:", `Từ ${from} Đến ${to}`],
        [], // Dòng trống
        ["TỔNG QUAN"],
        ["Tổng doanh thu", "Tổng đơn hàng", "Tổng giờ làm"],
        [revenue, orders, hours],
        [], // Dòng trống
        ["CHI TIẾT CA LÀM VIỆC"],
        ["Ngày", "Bắt đầu", "Kết thúc", "Thời lượng"]
    ];

    // C. Quét bảng Chi tiết chấm công
    const tableShifts = document.getElementById('tableShifts');
    if (tableShifts) {
        const shiftRows = tableShifts.querySelectorAll('tbody tr');
        shiftRows.forEach(row => {
            const cols = row.querySelectorAll('td');
            
            // Chỉ lấy dòng có đủ dữ liệu (4 cột) và không phải dòng thông báo "Không có dữ liệu"
            if (cols.length >= 4) { 
                const rowData = [
                    cols[0].innerText.trim(), // Ngày
                    cols[1].innerText.trim(), // Bắt đầu
                    cols[2].innerText.trim(), // Kết thúc
                    cols[3].innerText.trim()  // Thời lượng
                ];
                data.push(rowData);
            }
        });
    }

    // D. Tạo Workbook và Xuất file
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // (Tùy chọn) Thiết lập độ rộng cột cho đẹp
    ws['!cols'] = [{wch: 15}, {wch: 20}, {wch: 20}, {wch: 20}];

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "KPI_Report");

    // Tạo tên file: KPI_TenNhanVien_Ngay.xlsx (Thay khoảng trắng bằng _)
    const safeName = name.replace(/\s+/g, '_');
    const fileName = `KPI_${safeName}_${from}.xlsx`;
    
    XLSX.writeFile(wb, fileName);
}