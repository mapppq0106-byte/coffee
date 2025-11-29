<?php
/*
 * CORE CONTROLLER
 * Vai trò: Lớp cha (Base Class) mà tất cả Controller con phải kế thừa
 * Chức năng: Cung cấp các phương thức chung để gọi Model, View và các tiện ích bảo mật
 */
class Controller {

    // Constructor: Tự động chạy khi khởi tạo bất kỳ Controller nào
    public function __construct() {
        // Luôn kiểm tra trạng thái tài khoản mỗi khi tải trang
        $this->checkAccountStatus();
    }

    // Chức năng: Kiểm tra trạng thái tài khoản Real-time
    // Mục đích: Chặn ngay lập tức nếu tài khoản bị Admin khóa khi đang online
    protected function checkAccountStatus() {
        // Chỉ kiểm tra nếu người dùng đã đăng nhập
        if (isset($_SESSION['user_id'])) {
            // Khởi tạo kết nối DB trực tiếp để kiểm tra nhanh
            $db = new Database();
            $db->query("SELECT is_active FROM users WHERE user_id = :uid");
            $db->bind(':uid', $_SESSION['user_id']);
            $user = $db->single();

            // Nếu không tìm thấy user (đã bị xóa) hoặc bị khóa (is_active = 0)
            if (!$user || $user->is_active == 0) {
                // 1. Hủy phiên làm việc
                session_unset();
                session_destroy();
                
                // 2. Thông báo và đá về trang login bằng Javascript
                // (Dùng JS để đảm bảo chuyển hướng mượt mà và hiện alert)
                echo "<script>
                        alert('Phiên làm việc hết hạn hoặc tài khoản của bạn đã bị khóa!');
                        window.location.href = '" . URLROOT . "/auth/login';
                      </script>";
                exit; // Dừng ngay lập tức mọi xử lý phía sau
            }
        }
    }

    // Chức năng: Load Model và khởi tạo đối tượng
    // Tham số: $model (Tên file Model cần gọi, vd: 'ProductModel')
    public function model($model) {
        // Kiểm tra file Model có tồn tại không trước khi require
        if (file_exists('../app/models/' . $model . '.php')) {
            require_once '../app/models/' . $model . '.php';
            
            // Khởi tạo và trả về object của Model
            return new $model();
        } else {
            die("Model không tồn tại: " . $model);
        }
    }

    // Chức năng: Load View và truyền dữ liệu
    // Tham số: $view (Đường dẫn tới file View, vd: 'admin/products/index')
    //          $data (Mảng dữ liệu muốn hiển thị ở View)
    public function view($view, $data = []) {
        // Kiểm tra file View có tồn tại không
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            // Báo lỗi rõ ràng nếu đường dẫn sai
            die("View không tồn tại: " . $view);
        }
    }

    // Chức năng: Middleware bảo vệ (Chỉ cho phép Admin truy cập)
    // Thường được gọi trong __construct() của các Controller quản trị
    public function restrictToAdmin() {
        // Kiểm tra Session và Role ID (Giả định Role 1 là Admin)
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
            // Nếu không đủ quyền -> Chuyển hướng về trang đăng nhập
            header('location: ' . URLROOT . '/auth/login');
            exit;
        }
    }
}