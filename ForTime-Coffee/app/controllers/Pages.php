<?php
/*
 * PAGES CONTROLLER
 * Chức năng: Trang chủ/Trang thông tin chung của hệ thống
 * Quyền hạn: Tất cả tài khoản đã đăng nhập
 * Kết nối View: app/views/pages/index.php
 */
class Pages extends Controller {
    
    public function __construct() {
        // 1. Kiểm tra đăng nhập (Bảo mật cơ bản)
        // Nếu chưa đăng nhập -> Chuyển về trang Login
        if (!isset($_SESSION['user_id'])) {
            header('location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    // Chức năng: Hiển thị trang chào mừng/thông tin chung
    public function index() {
        // Chuẩn bị dữ liệu từ Session
        $data = [
            'title'     => 'Trang Quản Trị',
            'full_name' => $_SESSION['full_name'],
            'role_name' => ($_SESSION['role_id'] == 1) ? 'Quản trị viên (Admin)' : 'Nhân viên'
        ];

        // Gọi View hiển thị
        $this->view('pages/index', $data);
    }
}