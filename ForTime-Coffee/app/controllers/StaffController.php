<?php
/*
 * STAFF CONTROLLER
 * Chức năng: Quản lý nhân sự (Thêm, Sửa, Xóa, Phân quyền, Xem KPI)
 * Kết nối Model: app/models/UserModel.php
 * Kết nối View: app/views/admin/users/user_index.php, app/views/admin/users/stats_single.php
 */
class StaffController extends Controller {

    public function __construct() {
        // Bảo mật: Chỉ Admin mới được truy cập
        $this->restrictToAdmin();
        $this->userModel = $this->model('UserModel');
    }

    // Chức năng: Hiển thị danh sách nhân viên
    public function index() {
        $users = $this->userModel->getAllUsers();
        $data = ['users' => $users];
        $this->view('admin/users/user_index', $data);
    }

    // Chức năng: Thêm nhân viên mới
    // Xử lý: Validate tên đăng nhập -> Hash mật khẩu -> Lưu DB
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $username = trim($_POST['username']);

            // 1. Validate Username (Chỉ chữ, số, gạch dưới, không dấu, không khoảng trắng)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $_SESSION['error_username'] = 'Tên đăng nhập không hợp lệ (Không dấu, không khoảng trắng)!';
                header('location: ' . URLROOT . '/staff');
                exit;
            }

            // 2. Kiểm tra trùng lặp
            $existingUser = $this->userModel->findUserByUsername($username);
            if ($existingUser) {
                $_SESSION['error_username'] = 'Tên đăng nhập này đã tồn tại!';
                header('location: ' . URLROOT . '/staff');
                exit;
            }

            // 3. Chuẩn bị dữ liệu
            $data = [
                'username' => $username,
                'password' => password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
                'full_name' => trim($_POST['full_name']),
                'role_id' => trim($_POST['role_id']),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // 4. Lưu vào DB
            if ($this->userModel->addUser($data)) {
                header('location: ' . URLROOT . '/staff');
            } else {
                die('Lỗi hệ thống khi thêm nhân viên.');
            }
        }
    }

    // Chức năng: Cập nhật thông tin nhân viên
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $password = trim($_POST['password']);
            
            $data = [
                'id' => $id,
                'full_name' => trim($_POST['full_name']),
                'role_id' => trim($_POST['role_id']),
                'is_active' => $_POST['is_active'],
                // Nếu có nhập pass mới thì hash, không thì để trống (Model sẽ tự xử lý giữ pass cũ)
                'password' => !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : ''
            ];

            if ($this->userModel->updateUser($data)) {
                header('location: ' . URLROOT . '/staff');
            }
        }
    }

    // Chức năng: Xóa nhân viên
    public function delete($id) {
        // Bảo mật: Không cho phép tự xóa chính mình
        if ($id == $_SESSION['user_id']) {
            echo "<script>alert('Không thể xóa chính mình!'); window.location.href='".URLROOT."/staff';</script>";
            return;
        }

        if ($this->userModel->deleteUser($id)) {
            header('location: ' . URLROOT . '/staff');
        }
    }

    // Chức năng: Xem báo cáo hiệu suất (KPI) chi tiết của 1 nhân viên
    public function stats($id) {
        // Lấy ngày lọc (Mặc định từ đầu tháng đến nay)
        $fromDate = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
        $toDate = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');

        // Kiểm tra nhân viên có tồn tại không
        $user = $this->userModel->findUserById($id);
        if (!$user) {
            header('location: ' . URLROOT . '/staff');
            exit;
        }

        // Lấy dữ liệu thống kê từ Model
        $stats = $this->userModel->getStaffStatsById($id, $fromDate, $toDate);
        $shifts = $this->userModel->getShiftsByDate($id, $fromDate, $toDate);

        $data = [
            'user' => $user,
            'stats' => $stats,
            'shifts' => $shifts,
            'from_date' => $fromDate,
            'to_date' => $toDate
        ];

        $this->view('admin/users/stats_single', $data);
    }
}