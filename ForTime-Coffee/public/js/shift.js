/**
 * SHIFT REPORT JAVASCRIPT
 * -------------------------------------------------------------------------
 * - Chức năng: Xem chi tiết các món đã bán trong ca làm việc (Lịch sử kết ca).
 * - View sử dụng: app/views/shift/report.php
 * - Controller kết nối: Shift.php (Method: get_session_details)
 * - Model liên quan: CashModel.php
 * -------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
});

// ============================================================
// 1. INITIALIZATION MODULES
// ============================================================

function initSidebar() {
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    
    if(sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
}

// ============================================================
// 2. HELPER FUNCTIONS
// ============================================================

/**
 * Định dạng tiền tệ (VNĐ)
 */
const formatMoney = (amount) => parseInt(amount || 0).toLocaleString('vi-VN') + 'đ';

// ============================================================
// 3. GLOBAL FUNCTIONS (Gọi từ HTML onclick)
// ============================================================

/**
 * Gọi Ajax lấy chi tiết món bán trong ca và hiển thị Modal
 * @param {HTMLElement} element - Nút bấm chứa data attribute
 */
function viewSessionDetail(element) {
    // 1. Lấy dữ liệu từ các thuộc tính data-
    const start = element.getAttribute('data-start');
    const end = element.getAttribute('data-end');
    const id = element.getAttribute('data-id');
    const note = element.getAttribute('data-note');

    // 2. Cập nhật UI Modal (Header & Ghi chú)
    const idEl = document.getElementById('modalSessionId');
    if (idEl) idEl.innerText = id;

    const noteContainer = document.getElementById('modalSessionNote');
    if (noteContainer) {
        if (note && note.trim() !== "") {
            noteContainer.innerHTML = `
                <div class="alert alert-warning small fst-italic mb-3 shadow-sm">
                    <i class="fas fa-comment-alt me-2"></i> <strong>Ghi chú chốt ca:</strong> ${note}
                </div>`;
            noteContainer.style.display = 'block';
        } else {
            noteContainer.innerHTML = '';
            noteContainer.style.display = 'none';
        }
    }

    // 3. Reset bảng dữ liệu & Hiển thị loading
    const tbody = document.getElementById('modalItemsBody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Đang tải dữ liệu...</td></tr>';
    }
    
    // 4. Mở Modal
    const modalEl = document.getElementById('sessionDetailModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    // 5. Gọi API lấy dữ liệu
    const formData = new FormData();
    formData.append('start', start);
    formData.append('end', end);

    fetch(`${URLROOT}/shift/get_session_details`, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!tbody) return;

        if(data.items && data.items.length > 0) {
            
            // Render danh sách món ăn bằng map -> join
            const rowsHtml = data.items.map(item => {
                
                // [MỚI] Xử lý hiển thị Note (Size/Topping) nếu có
                let noteHtml = '';
                if (item.note && item.note.trim() !== '') {
                    noteHtml = `<div class="small text-muted fst-italic mt-1"><i class="fas fa-level-up-alt fa-rotate-90 me-1"></i> ${item.note}</div>`;
                }

                return `
                    <tr>
                        <td class="text-start ps-3 align-middle">
                            <div class="fw-bold text-primary">${item.product_name}</div>
                            ${noteHtml}
                        </td>
                        <td class="text-center fw-bold align-middle">${item.qty}</td>
                        <td class="text-end fw-bold text-danger pe-3 align-middle">${formatMoney(item.subtotal)}</td>
                    </tr>
                `;
            }).join('');
            
            tbody.innerHTML = rowsHtml;
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">Không có món nào được bán trong ca này.</td></tr>';
        }
    })
    .catch(err => {
        console.error(err);
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-3">Lỗi tải dữ liệu. Vui lòng thử lại.</td></tr>';
        }
    });
}