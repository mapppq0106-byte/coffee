/**
 * EXPORT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Xử lý xuất báo cáo (Excel, CSV, PDF) cho bảng dữ liệu.
 * - View sử dụng: app/views/admin/dashboard/index.php
 * - Controller kết nối: Dashboard.php
 * - Model liên quan: OrderModel.php (Dữ liệu đơn hàng)
 * -------------------------------------------------------------------------
 */

// ============================================================
// HELPER FUNCTIONS (Hàm hỗ trợ)
// ============================================================

/**
 * Tạo tên file kèm ngày tháng hiện tại
 * @param {string} extension - Đuôi file (xlsx, csv, pdf)
 */
function getFileName(extension) {
    const today = new Date();
    // Format: YYYY-MM-DD
    const dateStr = today.toISOString().split('T')[0]; 
    return `Danh_sach_don_hang_${dateStr}.${extension}`;
}

/**
 * Chuẩn bị bảng dữ liệu để xuất (Clone và lọc cột thừa)
 * @param {string} tableId - ID của bảng HTML
 */
function prepareTableForExport(tableId) {
    const table = document.getElementById(tableId);
    if (!table) {
        alert("Không tìm thấy dữ liệu bảng!");
        return null;
    }

    // Sao chép bảng để xử lý (tránh ảnh hưởng giao diện chính)
    const cloneTable = table.cloneNode(true);
    
    // Loại bỏ các cột có class .no-export (VD: Nút thao tác, checkbox)
    const noExports = cloneTable.querySelectorAll('.no-export');
    noExports.forEach(el => el.remove());

    return cloneTable;
}

// ============================================================
// MAIN EXPORT FUNCTIONS
// ============================================================

// 1. XUẤT EXCEL
function exportToExcel() {
    const cloneTable = prepareTableForExport("exportTable");
    if (!cloneTable) return;

    // Tạo workbook từ bảng đã xử lý
    const wb = XLSX.utils.table_to_book(cloneTable, {sheet: "Sheet1"});
    
    // Xuất file
    XLSX.writeFile(wb, getFileName('xlsx'));
}

// 2. XUẤT CSV
function exportToCSV() {
    const cloneTable = prepareTableForExport("exportTable");
    if (!cloneTable) return;

    // Tái sử dụng logic của SheetJS nhưng lưu đuôi .csv
    const wb = XLSX.utils.table_to_book(cloneTable, {sheet: "Sheet1"});
    XLSX.writeFile(wb, getFileName('csv'));
}

// 3. XUẤT PDF (Hỗ trợ tiếng Việt tốt)
function exportToPDF() {
    const element = document.getElementById('exportTable');
    if (!element) return;
    
    // Cấu hình PDF
    const opt = {
        margin:       10,
        filename:     getFileName('pdf'),
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 }, // Tăng độ nét (scale càng cao càng nét nhưng file nặng)
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' } // Khổ A4 dọc
    };

    // Bước 1: Ẩn cột thao tác trên giao diện thật trước khi chụp
    const noExports = document.querySelectorAll('.no-export');
    noExports.forEach(el => el.style.display = 'none');

    // Bước 2: Tạo PDF và Tải xuống
    html2pdf().set(opt).from(element).save()
    .then(function(){
        // Bước 3: Hiện lại cột thao tác sau khi in xong
        noExports.forEach(el => el.style.display = '');
    });
}