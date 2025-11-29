<?php
/*
 * TABLE CONTROLLER
 * Chức năng: Quản lý sơ đồ bàn (Thêm, Sửa, Xóa, Xem danh sách)
 * Kết nối Model: app/models/TableModel.php
 * Kết nối View: app/views/admin/tables/index.php
 */
class Table extends Controller {
    private $tableModel;

    public function __construct() {
        // Bảo mật: Chỉ Admin mới được truy cập
        $this->restrictToAdmin();
        
        // Load Model xử lý Bàn
        $this->tableModel = $this->model('TableModel');
    }

    // Chức năng: Hiển thị danh sách bàn
    public function index() {
        $tables = $this->tableModel->getTables();
        $this->view('admin/tables/index', ['tables' => $tables]);
    }

    // Chức năng: Thêm bàn mới

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['table_name']);
            
            // 1. Kiểm tra dữ liệu rỗng
            if (empty($name)) {
                $_SESSION['error_table_name'] = "Vui lòng nhập tên bàn!";
                header('location: ' . URLROOT . '/table');
                return;
            }

            // 2. Kiểm tra trùng tên (Gọi Model)
            if ($this->tableModel->checkTableNameExists($name)) {
                $_SESSION['error_table_name'] = "Tên bàn '$name' đã tồn tại!";
                header('location: ' . URLROOT . '/table');
                return;
            }
            
            // 3. Nếu hợp lệ thì thêm mới
            if ($this->tableModel->addTable($name)) {
                // Xóa lỗi cũ (nếu có) để không hiện lại
                unset($_SESSION['error_table_name']);
                header('location: ' . URLROOT . '/table');
            } else {
                die('Lỗi hệ thống khi thêm bàn');
            }
        } else {
            header('location: ' . URLROOT . '/table');
        }
    }

    // Chức năng: Cập nhật tên bàn

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['table_name']);
            
            // 1. Kiểm tra rỗng
            if (empty($name)) {
                $_SESSION['error_table_name'] = "Vui lòng nhập tên bàn!";
                header('location: ' . URLROOT . '/table');
                return;
            }

            // 2. [MỚI] Kiểm tra trùng tên (Trừ chính nó ra)
            if ($this->tableModel->checkTableNameExists($name, $id)) {
                $_SESSION['error_table_name'] = "Tên bàn '$name' đã tồn tại! Vui lòng chọn tên khác.";
                header('location: ' . URLROOT . '/table');
                return;
            }
            
            // 3. Cập nhật
            if ($this->tableModel->updateTable($id, $name)) {
                unset($_SESSION['error_table_name']); // Xóa lỗi cũ nếu thành công
                header('location: ' . URLROOT . '/table');
            } else {
                die('Lỗi hệ thống khi cập nhật bàn');
            }
        } else {
             header('location: ' . URLROOT . '/table');
        }
    }

    // Chức năng: Xóa bàn
    // Lưu ý: Việc xóa bàn có thể ảnh hưởng đến lịch sử đơn hàng gắn với bàn đó (nếu không dùng Soft Delete)
    public function delete($id) {
        $this->tableModel->deleteTable($id);
        redirect('table');
    }
}