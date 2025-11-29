<?php
/*
 * CATEGORY MODEL
 * Vai trò: Quản lý danh mục sản phẩm
 * Chức năng: CRUD (Tạo, Đọc, Cập nhật, Xóa mềm) bảng categories
 */
class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // 1. Lấy tất cả danh mục (Chỉ lấy những danh mục CHƯA BỊ XÓA - is_deleted = 0)
    public function getCategories() {
        // [CẬP NHẬT] Thêm điều kiện lọc is_deleted = 0
        $sql = "SELECT * FROM categories WHERE is_deleted = 0 ORDER BY category_id DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // 2. Thêm danh mục mới
    public function addCategory($name) {
        $sql = "INSERT INTO categories (category_name) VALUES (:name)";
        
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        
        return $this->db->execute();
    }

    // Kiểm tra tên danh mục đã tồn tại chưa
    // (Giữ nguyên logic check toàn bộ bảng để tránh lỗi Unique Key của DB)
    public function checkNameExists($name) {
        $sql = "SELECT * FROM categories WHERE category_name = :name";
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        $this->db->single();
        
        return $this->db->rowCount() > 0;
    }
    // [MỚI] Lấy tất cả danh mục kể cả đã xóa (Dùng cho form Admin)
public function getAllCategoriesIncludingDeleted() {
    $sql = "SELECT * FROM categories ORDER BY is_deleted ASC, category_id DESC";
    $this->db->query($sql);
    return $this->db->resultSet();
}

    // 3. Cập nhật tên danh mục
    public function updateCategory($id, $name) {
        $sql = "UPDATE categories 
                SET category_name = :name 
                WHERE category_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);
        
        return $this->db->execute();
    }

    // 4. Xóa danh mục (XÓA MỀM - SOFT DELETE)
    // [QUAN TRỌNG] Thay vì xóa vĩnh viễn, ta cập nhật trạng thái is_deleted = 1
    public function deleteCategory($id) {
        // Sửa câu lệnh DELETE thành UPDATE
        $sql = "UPDATE categories SET is_deleted = 1 WHERE category_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}