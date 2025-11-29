/**
 * CATEGORY MANAGEMENT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Quản lý Danh mục (Tìm kiếm, Chọn sửa, Xóa, Reset form).
 * - View sử dụng: app/views/admin/categories/index.php
 * - Controller kết nối: Category.php
 * - Model liên quan: CategoryModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initSearch();
    initDeleteAction();
});

// ============================================================
// 1. INITIALIZATION MODULES (Khởi tạo chức năng)
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
 * Module: Tìm kiếm nhanh danh mục trên bảng
 */
function initSearch() {
    const searchInput = document.getElementById('searchCat');
    const tableBody = document.getElementById('catTableBody');
    
    if(searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');
            
            // Duyệt qua các dòng để ẩn/hiện
            Array.from(rows).forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
}

/**
 * Module: Xử lý nút Xóa danh mục
 */
function initDeleteAction() {
    const btnDelete = document.getElementById('btnDelete');
    if(btnDelete) {
        btnDelete.addEventListener('click', function() {
            const id = document.getElementById('category_id').value;
            
            if(!id) {
                alert('Vui lòng chọn danh mục cần xóa!');
                return;
            }

            // Thông báo phù hợp với tính năng Xóa Mềm (Soft Delete)
            const msg = 'XÁC NHẬN XÓA:\n\n' + 
                        'Danh mục này sẽ bị ẩn khỏi danh sách chọn món mới.\n' + 
                        'Tuy nhiên, các món ăn và đơn hàng cũ thuộc danh mục này vẫn được giữ lại trong lịch sử.\n\n' + 
                        'Bạn có chắc chắn muốn xóa không?';

            if(confirm(msg)) {
                window.location.href = `${URLROOT}/category/delete/${id}`;
            }
        });
    }
}

// ============================================================
// 2. GLOBAL FUNCTIONS (Được gọi từ HTML onclick)
// ============================================================

/**
 * Đổ dữ liệu vào Form để chỉnh sửa
 * @param {Object} cat - Đối tượng danh mục chứa dữ liệu
 */
function editCategory(cat) {
    // Đổ dữ liệu vào input
    document.getElementById('category_id').value = cat.category_id;
    document.getElementById('category_name').value = cat.category_name;

    // Cập nhật action của form
    const form = document.getElementById('categoryForm');
    form.action = `${URLROOT}/category/edit/${cat.category_id}`;
    
    // Đổi giao diện nút Lưu -> Cập nhật
    const btnSave = document.getElementById('btnSave');
    btnSave.innerHTML = '<i class="fas fa-sync-alt"></i> Cập nhật';
    btnSave.classList.replace('btn-primary', 'btn-warning');
    btnSave.classList.add('text-white');
    
    // Hiện nút Xóa
    document.getElementById('btnDelete').classList.remove('d-none');
}

/**
 * Reset Form (Nút Làm mới)
 */
function resetCatForm() {
    // Xóa trắng form
    document.getElementById('categoryForm').reset();
    document.getElementById('category_id').value = '';
    
    // Trả lại action mặc định (Thêm mới)
    document.getElementById('categoryForm').action = `${URLROOT}/category/add`;
    
    // Trả lại giao diện nút Thêm mới
    const btnSave = document.getElementById('btnSave');
    btnSave.innerHTML = '<i class="fas fa-save"></i> Lưu lại';
    btnSave.classList.replace('btn-warning', 'btn-primary');
    btnSave.classList.remove('text-white');
    
    // Ẩn nút Xóa
    document.getElementById('btnDelete').classList.add('d-none');
}