/**
 * TABLE MANAGEMENT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Quản lý sơ đồ bàn (Thêm, Sửa tên bàn, Xóa bàn).
 * - View sử dụng: app/views/admin/tables/index.php
 * - Controller kết nối: Table.php
 * - Model liên quan: TableModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initSearch();
    initTableInteractions();
    initDeleteAction();
});

// ============================================================
// 1. INITIALIZATION MODULES
// ============================================================

/**
 * Module: Xử lý Toggle Sidebar
 */
function initSidebar() {
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    
    if(sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', () => sidebar.classList.toggle('active'));
    }
}

/**
 * Module: Tìm kiếm bàn nhanh trên giao diện
 */
function initSearch() {
    const searchInput = document.getElementById('searchTable');
    const tableBody = document.getElementById('tableTableBody');

    if(searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');
            
            // Chuyển HTMLCollection sang Array để duyệt
            Array.from(rows).forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
}

/**
 * Module: Xử lý sự kiện click vào dòng trong bảng (Chọn bàn để sửa)
 */
function initTableInteractions() {
    const tableBody = document.getElementById('tableTableBody');

    if(tableBody) {
        // Sử dụng Event Delegation: Gán sự kiện cho cha (tbody) thay vì từng dòng (tr)
        tableBody.addEventListener('click', function(e) {
            // Tìm thẻ tr có class .clickable-row gần nhất
            const row = e.target.closest('.clickable-row');
            
            if (row) {
                // 1. Xử lý Highlight giao diện
                const allRows = tableBody.querySelectorAll('.clickable-row');
                allRows.forEach(r => r.classList.remove('table-active'));
                row.classList.add('table-active');

                // 2. Lấy dữ liệu từ attribute data-json và đổ vào form
                try {
                    const jsonData = row.getAttribute('data-json');
                    if (jsonData) {
                        const tableData = JSON.parse(jsonData);
                        editTable(tableData);
                    }
                } catch (error) {
                    console.error('Lỗi phân tích dữ liệu bàn:', error);
                }
            }
        });
    }
}

/**
 * Module: Xử lý nút Xóa bàn
 */
function initDeleteAction() {
    const btnDelete = document.getElementById('btnDelete');
    
    if(btnDelete) {
        btnDelete.addEventListener('click', function() {
            const id = document.getElementById('table_id').value;
            
            if(!id) {
                alert('Vui lòng chọn một bàn để xóa!');
                return;
            }

            const confirmMsg = 'XÁC NHẬN XÓA BÀN:\n\n' +
                   'Bàn này sẽ bị ẩn khỏi sơ đồ.\n' +
                   'Lịch sử đơn hàng cũ của bàn vẫn được giữ nguyên.\n\n' +
                   'Bạn có chắc chắn muốn xóa không?';

            if(confirm(confirmMsg)) {
                window.location.href = `${URLROOT}/table/delete/${id}`;
            }
        });
    }
}

// ============================================================
// 2. GLOBAL FUNCTIONS (Hàm toàn cục)
// ============================================================

/**
 * Đổ dữ liệu bàn vào Form để chỉnh sửa
 * @param {Object} table - Object chứa thông tin bàn (id, name...)
 */
function editTable(table) {
    // Đổ dữ liệu vào input
    document.getElementById('table_id').value = table.table_id;
    document.getElementById('table_name').value = table.table_name;

    // Cập nhật action của form sang chế độ Edit
    const form = document.getElementById('tableForm');
    form.action = `${URLROOT}/table/edit/${table.table_id}`;
    
    // Đổi giao diện nút Lưu -> Cập nhật
    const btnSave = document.getElementById('btnSave');
    btnSave.innerHTML = '<i class="fas fa-sync-alt"></i> Cập nhật';
    btnSave.className = 'btn btn-warning text-white'; // Reset class và set mới cho gọn
    
    // Hiển thị nút Xóa
    document.getElementById('btnDelete').classList.remove('d-none');
}

/**
 * Reset Form về trạng thái thêm mới (Nút "Làm mới")
 */
function resetTableForm() {
    // Xóa trắng form
    document.getElementById('tableForm').reset();
    document.getElementById('table_id').value = '';
    
    // Reset action về mặc định (Add)
    document.getElementById('tableForm').action = `${URLROOT}/table/add`;
    
    // Xóa highlight trên bảng
    const activeRows = document.querySelectorAll('.table-active');
    activeRows.forEach(r => r.classList.remove('table-active'));
    
    // Đổi nút Cập nhật -> Lưu lại
    const btnSave = document.getElementById('btnSave');
    btnSave.innerHTML = '<i class="fas fa-save"></i> Lưu lại';
    btnSave.className = 'btn btn-primary';
    
    // Ẩn nút Xóa
    document.getElementById('btnDelete').classList.add('d-none');
}