<?php
/*
 * PROFILE CONTROLLER
 * Chức năng: Quản lý hồ sơ cá nhân (Xem thông tin, Doanh số, Chấm công, Đổi mật khẩu)
 * Kết nối Model: UserModel, OrderModel (và class Database)
 * Kết nối View: app/views/profile/index.php
 */
class Profile extends Controller {
    private $userModel;
    private $orderModel;

    public function __construct() {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('location: ' . URLROOT . '/auth/login');
            exit;
        }

        // 2. Load các Model cần thiết
        $this->userModel = $this->model('UserModel');
        $this->orderModel = $this->model('OrderModel');
    }

    // Chức năng: Hiển thị trang hồ sơ cá nhân
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // 1. Lấy thông tin user
        $user = $this->userModel->findUserById($userId);
        
        // 2. Tính doanh số cá nhân trong ngày
        $salesToday = $this->getPersonalSalesToday($userId);

        // 3. Lấy lịch sử chấm công
        $shifts = $this->userModel->getShiftHistory($userId);

        $data = [
            'user'        => $user,
            'sales_today' => $salesToday,
            'shifts'      => $shifts
        ];

        $this->view('profile/index', $data);
    }

    // Chức năng: Xử lý đổi mật khẩu
    public function change_password() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];
            $oldPass = $_POST['old_pass'];
            $newPass = $_POST['new_pass'];
            $confirmPass = $_POST['confirm_pass'];

            // 1. Kiểm tra xác nhận mật khẩu
            if ($newPass !== $confirmPass) {
                echo "<script>alert('Xác nhận mật khẩu mới không khớp!'); window.history.back();</script>";
                return;
            }

            // 2. Lấy thông tin user để check pass cũ
            $user = $this->userModel->findUserById($userId);
            
            if (password_verify($oldPass, $user->password_hash)) {
                // 3. Hash pass mới và cập nhật
                $newPassHash = password_hash($newPass, PASSWORD_DEFAULT);
                
                if ($this->userModel->changePassword($userId, $newPassHash)) {
                    echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='".URLROOT."/profile';</script>";
                } else {
                    echo "<script>alert('Lỗi hệ thống!'); window.history.back();</script>";
                }
            } else {
                echo "<script>alert('Mật khẩu cũ không đúng!'); window.history.back();</script>";
            }
        }
    }

    // Hàm phụ: Tính doanh số cá nhân trong ngày
    // (Sử dụng trực tiếp Database vì đây là query đặc thù cho trang Profile)
    private function getPersonalSalesToday($userId) {
        $db = new Database();
        $db->query("SELECT SUM(final_amount) as total 
                    FROM orders 
                    WHERE user_id = :uid AND status = 'paid' AND DATE(order_time) = CURDATE()");
        $db->bind(':uid', $userId);
        $row = $db->single();
        return $row->total ?? 0;
    }
}