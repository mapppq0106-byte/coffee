/**
 * PRODUCT MANAGEMENT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Xử lý form thêm/sửa/xóa món ăn và hiển thị ảnh preview.
 * - View sử dụng: app/views/admin/products/product_index.php
 * - Controller kết nối: ProductController.php
 * - Model liên quan: ProductModel.php, CategoryModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initSearch();
    initFormActions();
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
 * Module: Tìm kiếm nhanh trên bảng
 */
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('productTableBody');

    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');
            
            Array.from(rows).forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
}

/**
 * Module: Xử lý các nút trong Form (Sửa, Xóa)
 */
function initFormActions() {
    const btnEdit = document.getElementById('btnEdit');
    const btnDelete = document.getElementById('btnDelete');
    const productIdInput = document.getElementById('product_id');
    const form = document.getElementById('productForm');

    // Xử lý nút SỬA
    if (btnEdit) {
        btnEdit.addEventListener('click', function() {
            const id = productIdInput.value;
            if (!id) { 
                alert('Vui lòng chọn món để sửa (Click vào dòng trong bảng)!'); 
                return; 
            }
            
            // Đổi action của form sang route Edit
            form.action = `${URLROOT}/product/edit/${id}`;
            form.submit();
        });
    }

    // Xử lý nút XÓA
    if (btnDelete) {
        btnDelete.addEventListener('click', function() {
            const id = productIdInput.value;
            if (!id) { 
                alert('Vui lòng chọn món để xóa!'); 
                return; 
            }
            
            if (confirm('Bạn chắc chắn muốn xóa món này? Hành động này sẽ chuyển món vào thùng rác.')) {
                window.location.href = `${URLROOT}/product/delete/${id}`;
            }
        });
    }
}

// ============================================================
// 2. GLOBAL FUNCTIONS (Gọi từ HTML onclick)
// ============================================================

/**
 * Chọn sản phẩm từ bảng để đổ dữ liệu vào form
 * @param {HTMLElement} row - Dòng <tr> được click
 * @param {Object} product - Dữ liệu sản phẩm (JSON)
 */
function selectProduct(row, product) {
    // 1. Highlight dòng được chọn
    document.querySelectorAll('.table-row').forEach(r => r.classList.remove('active'));
    row.classList.add('active');

    // 2. Đổ dữ liệu vào Form
    document.getElementById('product_id').value = product.product_id;
    document.getElementById('product_name').value = product.product_name;
    document.getElementById('category_id').value = product.category_id;
    document.getElementById('price').value = product.price;
    document.getElementById('is_available').value = product.is_available;
    
    // 3. Hiển thị ảnh preview
    const previewDiv = document.getElementById('current_image_preview');
    if (previewDiv) {
        if (product.image) {
            previewDiv.innerHTML = `
                <img src="${URLROOT}/public/uploads/${product.image}" 
                     style="width: 80px; border-radius: 5px; border: 1px solid #ddd; object-fit: cover;">
                <div class="small text-muted mt-1">Ảnh hiện tại</div>
            `;
        } else {
            previewDiv.innerHTML = '<span class="text-muted small fst-italic">Chưa có hình ảnh</span>';
        }
    }
}

/**
 * Làm mới form để thêm món mới
 */
function resetForm() {
    // Reset các input
    document.getElementById('productForm').reset();
    document.getElementById('product_id').value = '';
    
    // Reset action về mặc định (Thêm mới)
    document.getElementById('productForm').action = `${URLROOT}/product/add`;

    // Xóa highlight trên bảng
    document.querySelectorAll('.table-row').forEach(r => r.classList.remove('active'));
    
    // Xóa ảnh preview
    const previewDiv = document.getElementById('current_image_preview');
    if (previewDiv) previewDiv.innerHTML = "";
}