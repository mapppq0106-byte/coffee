<?php
/*
 * PRODUCT MODEL
 * Vai trò: Quản lý Món ăn / Đồ uống (Sản phẩm)
 * Chức năng:
 * 1. Lấy danh sách món.
 * 2. Thêm, Sửa, Xóa món.
 * 3. Lấy danh sách Topping (Mới).
 */
class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // 1. Lấy danh sách sản phẩm (Chỉ lấy món CHƯA BỊ XÓA)
    public function getProducts() {
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.category_id 
                WHERE p.is_deleted = 0 
                ORDER BY p.product_id DESC";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // [QUAN TRỌNG] Hàm này đang thiếu nên gây lỗi
    // Lấy danh sách các món thuộc danh mục "Topping"
    public function getToppings() {
        $sql = "SELECT p.* FROM products p 
                JOIN categories c ON p.category_id = c.category_id 
                WHERE c.category_name LIKE '%Topping%' 
                AND p.is_available = 1 
                AND p.is_deleted = 0";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Kiểm tra tên món đã tồn tại chưa
// [CẬP NHẬT] Kiểm tra tên món đã tồn tại chưa
    // Thêm tham số $excludeId (mặc định là null) để dùng cho trường hợp Sửa
    public function checkNameExists($name, $excludeId = null) {
        $sql = "SELECT product_id FROM products WHERE product_name = :name AND is_deleted = 0";
        
        // Nếu có ID loại trừ (tức là đang Sửa), thêm điều kiện loại bỏ ID đó
        if ($excludeId) {
            $sql .= " AND product_id != :id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        
        $this->db->single();
        
        // Trả về true nếu tìm thấy tên trùng (ngoài trừ chính nó)
        return $this->db->rowCount() > 0;
    }

    // 2. Thêm món mới
    public function addProduct($data) {
        $sql = "INSERT INTO products (product_name, category_id, price, image, is_available) 
                VALUES (:name, :cat_id, :price, :image, :avail)";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':cat_id', $data['category_id']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':avail', $data['is_available']);

        return $this->db->execute();
    }

    // 3. Cập nhật thông tin món
    public function updateProduct($data) {
        if (!empty($data['image'])) {
            $sql = "UPDATE products 
                    SET product_name = :name, 
                        category_id = :cat_id, 
                        price = :price, 
                        image = :image, 
                        is_available = :avail 
                    WHERE product_id = :id";
        } else {
            $sql = "UPDATE products 
                    SET product_name = :name, 
                        category_id = :cat_id, 
                        price = :price, 
                        is_available = :avail 
                    WHERE product_id = :id";
        }

        $this->db->query($sql);
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':cat_id', $data['category_id']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':avail', $data['is_available']);

        if (!empty($data['image'])) {
            $this->db->bind(':image', $data['image']);
        }

        return $this->db->execute();
    }

    // 4. Xóa mềm (Soft Delete)
    public function deleteProduct($id) {
        $sql = "UPDATE products SET is_deleted = 1 WHERE product_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }
    
    // 5. Lấy thông tin chi tiết 1 món
    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE product_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
}