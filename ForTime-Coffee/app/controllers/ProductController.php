<?php
/*
 * PRODUCT CONTROLLER
 * Chức năng: Quản lý món ăn/đồ uống (Thêm, Sửa, Xóa, Hiển thị)
 * Kết nối Model: ProductModel, CategoryModel
 * Kết nối View: admin/products/product_index
 */
class ProductController extends Controller {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        // Bảo mật: Chỉ Admin mới được truy cập
        $this->restrictToAdmin();
        
        // Load các Model cần thiết
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    // Chức năng: Hiển thị danh sách sản phẩm
    public function index() {
        // 1. Lấy danh sách sản phẩm
        $products = $this->productModel->getProducts();
        
        // 2. Lấy TOÀN BỘ danh mục để hiển thị đúng trong Form Sửa
        $categories = $this->categoryModel->getAllCategoriesIncludingDeleted();

        $data = [
            'products' => $products,
            'categories' => $categories
        ];

        $this->view('admin/products/product_index', $data);
    }

    // Chức năng: Thêm món mới
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lọc dữ liệu đầu vào
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $name = trim($_POST['product_name']);
            $price = (float)$_POST['price']; 
            $categoryId = trim($_POST['category_id']);
            $isAvailable = isset($_POST['is_available']) ? 1 : 0;

            // --- VALIDATE DỮ LIỆU ---

            // A. Kiểm tra tên món
            if (empty($name)) {
                $_SESSION['error_product_name'] = "Vui lòng nhập tên món!";
                header('location: ' . URLROOT . '/product');
                return;
            }

            // Kiểm tra trùng tên (Add: check toàn bộ)
            if ($this->productModel->checkNameExists($name)) {
                $_SESSION['error_product_name'] = "Món '$name' đã tồn tại!";
                header('location: ' . URLROOT . '/product');
                return;
            }

            // B. Kiểm tra giá bán
            if ($price <= 0) {
                $_SESSION['error_product_price'] = "Giá bán phải lớn hơn 0!";
                header('location: ' . URLROOT . '/product');
                return;
            }

            // --- XỬ LÝ DỮ LIỆU ---
            $data = [
                'name' => $name,
                'category_id' => $categoryId,
                'price' => $price,
                'is_available' => $isAvailable,
                'image' => ''
            ];

            // Xử lý upload ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $imgName = time() . '_' . $_FILES['image']['name'];
                $uploadPath = '../public/uploads/' . $imgName;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image'] = $imgName;
                }
            }

            if ($this->productModel->addProduct($data)) {
                header('location: ' . URLROOT . '/product');
            } else {
                die('Lỗi hệ thống khi thêm sản phẩm');
            }
        } else {
            header('location: ' . URLROOT . '/product');
        }
    }

    // Chức năng: Cập nhật thông tin món
// Chức năng: Cập nhật thông tin món
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $name = trim($_POST['product_name']);
            $price = (float)$_POST['price'];

            // --- 1. VALIDATE DỮ LIỆU (Sử dụng Session thay vì Alert) ---

            // Validate giá bán
            if ($price <= 0) {
                $_SESSION['error_product_price'] = "Giá bán phải lớn hơn 0!";
                header('location: ' . URLROOT . '/product');
                return;
            }

            // Validate trùng tên (Trừ chính nó ra)
            if ($this->productModel->checkNameExists($name, $id)) {
                $_SESSION['error_product_name'] = "Tên món \"$name\" đã tồn tại! Vui lòng đặt tên khác.";
                header('location: ' . URLROOT . '/product');
                return;
            }

            // --- 2. XỬ LÝ CẬP NHẬT ---
            $data = [
                'id' => $id,
                'name' => $name,
                'category_id' => trim($_POST['category_id']),
                'price' => $price,
                'is_available' => $_POST['is_available'],
                'image' => '' 
            ];

            // Xử lý upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $imgName = time() . '_' . $_FILES['image']['name'];
                $uploadPath = '../public/uploads/' . $imgName;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $data['image'] = $imgName;
                }
            }

            if ($this->productModel->updateProduct($data)) {
                header('location: ' . URLROOT . '/product');
            } else {
                die('Lỗi cập nhật sản phẩm');
            }
        } else {
            header('location: ' . URLROOT . '/product');
        }
    }

    // Chức năng: Xóa món (Xóa mềm)
    public function delete($id) {
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            if ($this->productModel->deleteProduct($id)) {
                // Xóa file ảnh vật lý (nếu muốn dọn dẹp)
                if (!empty($product->image)) {
                    $imagePath = '../public/uploads/' . $product->image;
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                header('location: ' . URLROOT . '/product');
            } else {
                die('Có lỗi khi xóa sản phẩm');
            }
        } else {
            header('location: ' . URLROOT . '/product');
        }
    }
}