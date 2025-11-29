<?php
/*
 * URL HELPER
 * Vai trò: Chứa các hàm hỗ trợ xử lý URL và điều hướng trang
 */

// Chức năng: Chuyển hướng (Redirect) sang trang khác
// Tham số: $page (Đường dẫn muốn đến, ví dụ: 'auth/login' hoặc 'dashboard')
function redirect($page) {
    // Gửi header yêu cầu trình duyệt chuyển sang địa chỉ mới
    header('location: ' . URLROOT . '/' . $page);
    
    // QUAN TRỌNG: Dừng thực thi script ngay lập tức
    // Ngăn chặn các đoạn code phía sau chạy tiếp (tránh lỗi logic và bảo mật)
    exit;
}