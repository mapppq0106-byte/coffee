/**
 * STAFF MANAGEMENT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Xử lý sự kiện cho trang Quản lý Nhân viên (Thêm, Sửa, Xóa, Chọn từ bảng)
 * - View sử dụng: app/views/admin/users/user_index.php
 * - Controller kết nối: StaffController.php
 * - Model liên quan: UserModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initFormActions();
});

// ============================================================
// 1. INITIALIZATION MODULES
// ============================================================

function initSidebar() {
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    if(sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', () => sidebar.classList.toggle('active'));
    }
}

function initFormActions() {
    const btnEdit = document.getElementById('btnEdit');
    const btnDelete = document.getElementById('btnDelete');
    const userIdInput = document.getElementById('user_id');
    const form = document.getElementById('userForm');

    if (btnEdit) {
        btnEdit.addEventListener('click', function() {
            const id = userIdInput.value;
            if(!id) { 
                alert('Vui lòng chọn nhân viên cần sửa từ danh sách bên phải!'); 
                return; 
            }
            form.action = `${URLROOT}/staff/edit/${id}`;
            form.submit();
        });
    }

    if (btnDelete) {
        btnDelete.addEventListener('click', function() {
            const id = userIdInput.value;
            if(!id) { 
                alert('Vui lòng chọn nhân viên cần xóa!'); 
                return; 
            }
            if(confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa tài khoản này không?')) {
                window.location.href = `${URLROOT}/staff/delete/${id}`;
            }
        });
    }
}

// ============================================================
// 2. GLOBAL FUNCTIONS (Gọi từ HTML onclick)
// ============================================================

/**
 * Chọn nhân viên từ bảng để đổ dữ liệu vào form (CHẾ ĐỘ SỬA)
 * @param {HTMLElement} row - Dòng <tr> được click
 * @param {Object} user - Dữ liệu user (JSON)
 */
function selectUser(row, user) {
    // 1. Highlight dòng được chọn
    document.querySelectorAll('.table-row').forEach(r => r.classList.remove('active'));
    row.classList.add('active');

    // 2. Đổ dữ liệu vào form
    document.getElementById('user_id').value = user.user_id;
    
    const usernameInput = document.getElementById('username');
    usernameInput.value = user.username;
    usernameInput.readOnly = true; // Không cho sửa username khi edit
    
    document.getElementById('full_name').value = user.full_name;
    document.getElementById('role_id').value = user.role_id;
    document.getElementById('is_active').value = user.is_active;

    // [MỚI] Ẩn ô mật khẩu khi đang ở chế độ Sửa
    // Admin không được quyền đổi pass của nhân viên tại đây
    const divPass = document.getElementById('div-password');
    if (divPass) {
        divPass.style.display = 'none';
        document.getElementById('password').value = ''; // Xóa giá trị để đảm bảo an toàn
    }
}

/**
 * Làm mới form (CHẾ ĐỘ THÊM MỚI)
 */
function resetForm() {
    // Reset toàn bộ form
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
    
    // Cho phép nhập username mới
    document.getElementById('username').readOnly = false; 
    
    // Xóa highlight trên bảng
    document.querySelectorAll('.table-row').forEach(r => r.classList.remove('active'));
    
    // Mặc định trạng thái là Hoạt động (1)
    document.getElementById('is_active').value = 1;

    // [MỚI] Hiện lại ô mật khẩu khi ở chế độ Thêm mới
    const divPass = document.getElementById('div-password');
    if (divPass) {
        divPass.style.display = 'block';
    }
}