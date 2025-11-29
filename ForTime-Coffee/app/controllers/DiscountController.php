<?php
/*
 * DISCOUNT CONTROLLER
 * Chức năng: Quản lý mã giảm giá (Thêm, Xóa, Bật/Tắt trạng thái)
 * Kết nối Model: app/models/DiscountModel.php
 * Kết nối View: app/views/admin/discounts/index.php
 */
class DiscountController extends Controller {
    private $discountModel;

    public function __construct() {
        // Bảo mật: Chỉ Admin mới được truy cập
        $this->restrictToAdmin();
        $this->discountModel = $this->model('DiscountModel');
    }

    // Chức năng: Hiển thị danh sách mã giảm giá
    public function index() {
        $discounts = $this->discountModel->getAllDiscounts();
        $data = ['discounts' => $discounts];
        $this->view('admin/discounts/index', $data);
    }

    // Chức năng: Thêm mã giảm giá mới
    // Xử lý: Validate dữ liệu -> Kiểm tra trùng mã -> Lưu vào DB

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $code = strtoupper(trim($_POST['code']));
            $type = $_POST['type'];
            $value = (float)$_POST['value']; // Ép kiểu số thực để so sánh
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // 1. Xử lý điều kiện (Mới thêm)
        $condition_type = $_POST['condition_type']; // 'none' hoặc 'min'
        $min_order_value = 0;

        if ($condition_type == 'min') {
            $min_order_value = (float)$_POST['min_order_value'];
            if ($min_order_value <= 0) {
                $_SESSION['error_discount_min'] = "Số tiền đơn hàng tối thiểu phải lớn hơn 0!";
                header('location: ' . URLROOT . '/discount');
                return;
            }
        }


            // --- 1. VALIDATE DỮ LIỆU ---
            
            // Kiểm tra Mã Code
            if (empty($code)) {
                $_SESSION['error_discount_code'] = "Vui lòng nhập Mã giảm giá!";
                header('location: ' . URLROOT . '/discount');
                return;
            }

            if ($this->discountModel->checkCodeExists($code)) {
                $_SESSION['error_discount_code'] = "Mã '$code' đã tồn tại!";
                header('location: ' . URLROOT . '/discount');
                return;
            }

            // Kiểm tra Giá trị (Logic bạn cần đây)
            if ($value <= 0) {
                $_SESSION['error_discount_value'] = "Giá trị phải lớn hơn 0!";
                header('location: ' . URLROOT . '/discount');
                return;
            }

            if ($type == 'percentage' && $value > 100) {
                $_SESSION['error_discount_value'] = "Giảm giá phần trăm không được quá 100%!";
                header('location: ' . URLROOT . '/discount');
                return;
            }

            // --- 2. THÊM VÀO DB (Nếu không có lỗi) ---
            $data = [
                'code' => $code,
                'type' => $type,
                'value' => $value,
                'min_order_value' => $min_order_value,
                'is_active' => $is_active
            ];

            if ($this->discountModel->addDiscount($data)) {
                header('location: ' . URLROOT . '/discount');
            } else {
                die('Lỗi hệ thống khi thêm mã.');
            }
        }
    }

    // Chức năng: Xóa mã giảm giá vĩnh viễn
    // Lưu ý: Cần cảnh báo người dùng trước khi xóa (đã xử lý ở View)
    public function delete($id) {
        if ($this->discountModel->deleteDiscount($id)) {
            header('location: ' . URLROOT . '/discount');
        }
    }

    // Chức năng: Bật/Tắt trạng thái hoạt động (Active/Inactive)
    // Tham số: $id (Mã ID), $status (1: Bật, 0: Tắt)
    public function toggle($id, $status) {
        if ($this->discountModel->updateStatus($id, $status)) {
            header('location: ' . URLROOT . '/discount');
        }
    }
}