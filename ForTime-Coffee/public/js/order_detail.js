/**
 * ORDER DETAIL JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Gọi API lấy chi tiết đơn hàng và hiển thị lên Modal.
 * - View sử dụng: app/views/admin/dashboard/index.php (và modal_detail.php)
 * - Controller kết nối: Dashboard.php (Method: get_order_detail)
 * - Model liên quan: OrderModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // -----------------------------------------------------------
    // 1. LẮNG NGHE SỰ KIỆN CLICK TRÊN BẢNG ĐƠN HÀNG
    // -----------------------------------------------------------
    const tableBody = document.getElementById('ordersTableBody');
    if (tableBody) {
        tableBody.addEventListener('click', function(e) {
            // Tìm dòng (tr) chứa phần tử được click
            // Hỗ trợ click vào bất kỳ đâu trên dòng hoặc nút xem chi tiết
            const row = e.target.closest('tr.clickable-row') || e.target.closest('button')?.closest('tr');
            
            if (row && row.dataset.id) {
                const orderId = row.dataset.id;
                showOrderDetail(orderId);
            }
        });
    }

    // -----------------------------------------------------------
    // 2. XỬ LÝ TOGGLE SIDEBAR (Nếu chưa được xử lý ở file khác)
    // -----------------------------------------------------------
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});

// ============================================================
// HELPER FUNCTIONS (Hàm hỗ trợ định dạng)
// ============================================================

/**
 * Định dạng số tiền sang tiếng Việt (VD: 100.000đ)
 */
function formatMoney(amount) {
    return parseInt(amount || 0).toLocaleString('vi-VN') + 'đ';
}

/**
 * Định dạng thời gian (VD: 10:30 20/11/2023)
 */
function formatDate(dateString) {
    try {
        const date = new Date(dateString);
        // Kiểm tra nếu date không hợp lệ
        if (isNaN(date.getTime())) return dateString;
        return date.toLocaleString('vi-VN');
    } catch (e) {
        return dateString;
    }
}

// ============================================================
// MAIN LOGIC (Xử lý Modal)
// ============================================================

/**
 * Gọi API lấy chi tiết đơn hàng và hiển thị Modal
 * @param {string|number} orderId - ID đơn hàng cần xem
 */
function showOrderDetail(orderId) {
    // Hiển thị trạng thái đang tải
    document.body.style.cursor = 'wait';

    // Gọi API lấy dữ liệu
    fetch(`${URLROOT}/dashboard/get_order_detail/${orderId}`)
        .then(response => {
            if (!response.ok) throw new Error('Lỗi kết nối mạng');
            return response.json();
        })
        .then(data => {
            document.body.style.cursor = 'default';
            renderModalData(data);
        })
        .catch(error => {
            console.error('Lỗi chi tiết:', error);
            document.body.style.cursor = 'default';
            alert('Không thể tải chi tiết đơn hàng. Vui lòng thử lại sau.');
        });
}

/**
 * Đổ dữ liệu vào các phần tử trong Modal
 * @param {object} data - Dữ liệu JSON trả về từ API
 */
function renderModalData(data) {
    const info = data.info || {};
    const items = data.items || [];

    // 1. Điền thông tin chung (Header)
    setText('modalOrderId', '#' + (info.order_id || 'N/A'));
    setText('modalStaffName', info.staff_name || 'Khách vãng lai');
    setText('modalTime', formatDate(info.order_time));

    // 2. Xử lý hiển thị Tổng tiền & Giảm giá
    const total = parseInt(info.total_amount || 0);
    const final = parseInt(info.final_amount || total);
    const modalTotalEl = document.getElementById('modalTotal');

    if (modalTotalEl) {
        if (total > final) {
            // Trường hợp có giảm giá: Hiện giá gốc gạch ngang + giá thực
            modalTotalEl.innerHTML = `
                <div class="d-flex flex-column align-items-end">
                    <small class="text-decoration-line-through text-muted" style="font-size: 0.85rem;">${formatMoney(total)}</small>
                    <span class="text-danger fw-bold">${formatMoney(final)}</span>
                </div>`;
        } else {
            // Trường hợp bình thường
            modalTotalEl.innerText = formatMoney(final);
        }
    }

    // 3. Render danh sách món ăn
    const tbody = document.getElementById('modalOrderItems');
    if (tbody) {
        if (items.length > 0) {
            // Tạo HTML cho từng món
            const itemsHtml = items.map(item => {
                const subtotal = item.quantity * item.unit_price;
                
                // [MỚI] Xử lý hiển thị Note (Size/Topping) bên cạnh tên món
                let productNameDisplay = item.product_name;
                if (item.note && item.note.trim() !== '') {
                    // Thêm note vào trong ngoặc đơn, style màu xanh nhẹ
                    productNameDisplay += ` <span class="text-success fst-italic small">(${item.note})</span>`;
                }

                return `
                    <tr>
                        <td class="fw-bold text-primary">${productNameDisplay}</td>
                        <td class="text-center fw-bold">${item.quantity}</td>
                        <td class="text-end text-muted">${formatMoney(item.unit_price)}</td>
                        <td class="text-end fw-bold text-dark">${formatMoney(subtotal)}</td>
                    </tr>`;
            }).join('');

            tbody.innerHTML = itemsHtml;

            // Thêm dòng giảm giá (nếu có)
            if (total > final) {
                const discountVal = total - final;
                const discountRow = `
                    <tr class="bg-light">
                        <td colspan="3" class="text-end fw-bold text-success fst-italic">
                            <i class="fas fa-tag me-1"></i> Giảm giá / Khuyến mãi:
                        </td>
                        <td class="text-end fw-bold text-success">-${formatMoney(discountVal)}</td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', discountRow);
            }
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Không có thông tin món ăn.</td></tr>';
        }
    }

    // 4. Mở Modal
    const modalElement = document.getElementById('orderDetailModal');
    if (modalElement) {
        // Kiểm tra xem modal đã được khởi tạo chưa để tránh lỗi đè instance
        let myModal = bootstrap.Modal.getInstance(modalElement);
        if (!myModal) {
            myModal = new bootstrap.Modal(modalElement);
        }
        myModal.show();
    }
}

/**
 * Hàm phụ: Gán text cho element an toàn (tránh lỗi null)
 */
function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.innerText = text;
}