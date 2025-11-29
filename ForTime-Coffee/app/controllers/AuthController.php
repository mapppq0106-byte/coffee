<?php
/*
 * AUTH CONTROLLER
 * Chức năng: Xử lý xác thực người dùng (Đăng nhập, Đăng xuất, Phân quyền)
 * Kết nối Model: app/models/UserModel.php
 * Kết nối View: app/views/auth/login.php
 */
class AuthController extends Controller {

    public function __construct() {
        // Load Model User để kiểm tra DB
        $this->userModel = $this->model('UserModel');
    }

    /**
     * Chức năng: Xử lý Đăng nhập
     * - Kiểm tra input, gọi Model xác thực
     * - Nếu đúng: Ghi nhận bắt đầu ca làm -> Tạo session -> Chuyển hướng
     * - Nếu sai: Báo lỗi ra View
     * Kết nối View: app/views/auth/login.php
     */
    public function login() {
        $data = [
            'username' => '',
            'password' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lọc dữ liệu đầu vào
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data['username'] = trim($_POST['username']);
            $data['password'] = trim($_POST['password']);

            if (empty($data['username']) || empty($data['password'])) {
                $data['error'] = 'Vui lòng nhập đầy đủ thông tin.';
            } else {
                // Gọi hàm login từ Model
                $loggedInUser = $this->userModel->login($data['username'], $data['password']);

                if ($loggedInUser) {
                    // 1. Ghi nhận bắt đầu ca làm việc (Shift)
                    $this->userModel->startShift($loggedInUser->user_id);
                    
                    // 2. Tạo session và chuyển hướng
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['error'] = 'Sai tài khoản hoặc mật khẩu, hoặc tài khoản bị khóa.';
                }
            }
        }

        // Load View đăng nhập
        $this->view('auth/login', $data);
    }

    /**
     * Chức năng: Tạo Session và Điều hướng theo quyền
     * - Admin (Role 1) -> Dashboard
     * - Staff (Role 2) -> POS
     * Kết nối: app/controllers/Dashboard.php HOẶC app/controllers/PosController.php
     */
    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['username'] = $user->username;
        $_SESSION['full_name'] = $user->full_name;
        $_SESSION['role_id'] = $user->role_id;

        if ($user->role_id == 1) {
            header('location: ' . URLROOT . '/dashboard');
        } else {
            header('location: ' . URLROOT . '/pos');
        }
    }

    /**
     * Chức năng: Đăng xuất
     * - Ghi nhận kết thúc ca làm việc
     * - Hủy session
     * - Quay về trang login
     * Kết nối View: app/views/auth/login.php
     */
    public function logout() {
        // Chốt ca làm việc trước khi hủy session
        if (isset($_SESSION['user_id'])) {
            $this->userModel->endShift($_SESSION['user_id']);
        }

        // Xóa toàn bộ session
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['full_name']);
        unset($_SESSION['role_id']);
        session_destroy();

        // Quay về trang đăng nhập
        header('location: ' . URLROOT . '/auth/login');
    }
}