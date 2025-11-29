<?php
/*
 * MENU CONTROLLER
 * Chức năng: Hiển thị danh sách thực đơn (Menu) cho nhân viên/admin xem
 * Quyền hạn: Tất cả tài khoản đã đăng nhập
 * Kết nối Model: app/models/CategoryModel.php, app/models/ProductModel.php
 * Kết nối View: app/views/menu/index.php
 */
class MenuController extends Controller {
    
    // Khai báo các model sẽ sử dụng
    private $categoryModel;
    private $productModel;

    public function __construct() {
        // 1. Kiểm tra đăng nhập (Bảo mật cơ bản)
        // Nếu chưa đăng nhập -> Chuyển về trang Login
        if (!isset($_SESSION['user_id'])) {
            header('location: ' . URLROOT . '/auth/login');
            exit;
        }

        // 2. Load các Model cần thiết
        $this->categoryModel = $this->model('CategoryModel');
        $this->productModel = $this->model('ProductModel');
    }

    // Chức năng: Lấy dữ liệu và hiển thị trang Menu
    public function index() {
        // Lấy danh sách danh mục và sản phẩm từ DB
        $categories = $this->categoryModel->getCategories();
        $products = $this->productModel->getProducts();

        // Đóng gói dữ liệu gửi sang View
        $data = [
            'categories' => $categories,
            'products' => $products
        ];

        // Gọi View hiển thị
        $this->view('menu/index', $data);
    }
}