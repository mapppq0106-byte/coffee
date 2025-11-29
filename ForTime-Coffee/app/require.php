<?php
/*
 * REQUIRE / BOOTSTRAP FILE
 * Vai trò: Khởi động hệ thống, thiết lập môi trường và load các file cốt lõi.
 * File này được gọi đầu tiên bởi public/index.php
 */

// =========================================================================
// 1. THIẾT LẬP MÔI TRƯỜNG (ENVIRONMENT)
// =========================================================================

// Đặt múi giờ chuẩn Việt Nam để lưu giờ order/check-in chính xác
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Khởi động Session (Lưu trạng thái đăng nhập, giỏ hàng...)
// Kiểm tra nếu chưa có session thì mới start (Tránh lỗi "Session already started")
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// 2. LOAD CÁC FILE HỆ THỐNG (CORE FILES)
// =========================================================================

// A. Cấu hình (Quan trọng nhất, phải load đầu tiên)
require_once 'config/config.php';

// B. Các hàm hỗ trợ (Helpers)
require_once 'helpers/url_helper.php';

// C. Các lớp lõi (Core Classes)
// Database: Để các Model kết nối CSDL
require_once 'core/Database.php';

// Controller: Lớp cha của mọi Controller
require_once 'core/Controller.php';

// App: Bộ định tuyến (Router) xử lý URL
require_once 'core/App.php';