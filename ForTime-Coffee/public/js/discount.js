/**
 * DISCOUNT MANAGEMENT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Xử lý sự kiện cho trang Quản lý Mã giảm giá (Đơn vị tiền tệ, Điều kiện áp dụng).
 * - View sử dụng: app/views/admin/discounts/index.php
 * - Controller kết nối: DiscountController.php
 * - Model liên quan: DiscountModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initDiscountTypeLogic();
    initConditionLogic();
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
    
    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
}

/**
 * Module: Xử lý thay đổi đơn vị (VNĐ / %) khi chọn loại giảm giá
 */
function initDiscountTypeLogic() {
    const typeSelect = document.querySelector('select[name="type"]');
    const unitSpan = document.getElementById('value-unit');

    if (typeSelect && unitSpan) {
        typeSelect.addEventListener('change', function() {
            // Nếu chọn 'percentage' -> Hiển thị %, ngược lại hiển thị VNĐ
            unitSpan.innerText = (this.value === 'percentage') ? '%' : 'VNĐ';
        });
    }
}

/**
 * Module: Xử lý ẩn/hiện ô nhập "Điều kiện đơn hàng tối thiểu"
 */
function initConditionLogic() {
    const radioNone = document.getElementById('cond_none');
    const radioMin = document.getElementById('cond_min');
    const boxMin = document.getElementById('box-min-value');

    if(radioNone && radioMin && boxMin) {
        // Hàm xử lý logic hiển thị
        const toggleBox = () => {
            const input = boxMin.querySelector('input');
            
            if (radioMin.checked) {
                // Nếu chọn "Có điều kiện" -> Hiện ô nhập & Bắt buộc nhập
                boxMin.style.display = 'block';
                if(input) input.setAttribute('required', 'required');
            } else {
                // Nếu chọn "Không điều kiện" -> Ẩn ô nhập & Xóa giá trị
                boxMin.style.display = 'none';
                if(input) {
                    input.removeAttribute('required');
                    input.value = ''; 
                }
            }
        };

        // Gán sự kiện khi thay đổi lựa chọn
        radioNone.addEventListener('change', toggleBox);
        radioMin.addEventListener('change', toggleBox);
        
        // Chạy 1 lần lúc tải trang (để giữ trạng thái nếu form load lại do lỗi)
        toggleBox();
    }
}