<?php
/*
 * CATEGORY CONTROLLER
 * Chức năng: Quản lý danh mục sản phẩm (Thêm, Sửa, Xóa, Xem danh sách)
 * Kết nối Model: app/models/CategoryModel.php
 * Kết nối View: app/views/admin/categories/index.php
 */
class Category extends Controller {
    private $categoryModel;

    public function __construct() {
        // Bảo mật: Chỉ Admin mới được truy cập
        $this->restrictToAdmin(); 
        
        // Load Model xử lý danh mục
        $this->categoryModel = $this->model('CategoryModel');
    }

    // Chức năng: Hiển thị danh sách danh mục
    public function index() {
        $categories = $this->categoryModel->getCategories();
        $this->view('admin/categories/index', ['categories' => $categories]);
    }

    // Chức năng: Thêm danh mục mới
public function add() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = trim($_POST['category_name']);
        
        if (!empty($name)) {
            // 1. Kiểm tra trùng tên
            if ($this->categoryModel->checkNameExists($name)) {
                // LƯU LỖI VÀO SESSION
                $_SESSION['error_category'] = "Lỗi: Tên danh mục '$name' đã tồn tại!";
            } else {
                // 2. Nếu không trùng thì thêm mới
                $this->categoryModel->addCategory($name);
                // Xóa lỗi cũ nếu có (để tránh hiện lại khi thành công)
                unset($_SESSION['error_category']);
            }
        }
        
        // Quay lại trang danh sách (Không dùng history.back nữa)
        redirect('category');
    }
}

    // Chức năng: Cập nhật tên danh mục
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['category_name']);
            
            if (!empty($name)) {
                $this->categoryModel->updateCategory($id, $name);
            }
            
            redirect('category');
        }
    }

    // Chức năng: Xóa danh mục
    public function delete($id) {
        $this->categoryModel->deleteCategory($id);
        redirect('category');
    }
}