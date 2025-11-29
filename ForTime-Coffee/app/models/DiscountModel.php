<?php
/*
 * DISCOUNT MODEL
 * Vai trò: Quản lý Mã giảm giá (Coupon/Voucher)
 * Chức năng:
 * 1. CRUD Mã giảm giá.
 * 2. Kiểm tra và áp dụng mã giảm giá cho đơn hàng.
 * 3. Kích hoạt hoặc vô hiệu hóa mã.
 */
class DiscountModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // -------------------------------------------------------------------------
    // CÁC HÀM TRUY VẤN DỮ LIỆU (GET)
    // -------------------------------------------------------------------------

    // 1. Tìm mã giảm giá theo Code (Dùng khi khách nhập mã tại POS)
    // Lưu ý: Chỉ lấy mã đang hoạt động (is_active = 1)
    public function getDiscountByCode($code) {
        $sql = "SELECT * FROM discounts 
                WHERE code = :code AND is_active = 1";
        
        $this->db->query($sql);
        $this->db->bind(':code', $code);
        
        return $this->db->single();
    }

    // 2. Lấy thông tin chi tiết mã theo ID
    public function getDiscountById($id) {
        $sql = "SELECT * FROM discounts WHERE discount_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }

    // 3. Lấy danh sách tất cả mã giảm giá (Cho trang quản trị)
    public function getAllDiscounts() {
        $sql = "SELECT * FROM discounts ORDER BY discount_id DESC";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // 4. Kiểm tra mã có tồn tại chưa (Tránh trùng lặp khi tạo mới)
    public function checkCodeExists($code) {
        $sql = "SELECT * FROM discounts WHERE code = :code";
        
        $this->db->query($sql);
        $this->db->bind(':code', $code);
        
        return $this->db->single();
    }

    // -------------------------------------------------------------------------
    // CÁC HÀM TÁC ĐỘNG DỮ LIỆU (INSERT, UPDATE, DELETE)
    // -------------------------------------------------------------------------

    // 5. Thêm mã giảm giá mới
public function addDiscount($data) {
    $sql = "INSERT INTO discounts (code, type, value, min_order_value, is_active) 
            VALUES (:code, :type, :val, :min_order, :active)";
    
    $this->db->query($sql);
    $this->db->bind(':code', $data['code']);
    $this->db->bind(':type', $data['type']);
    $this->db->bind(':val', $data['value']);
    // Bind thêm tham số mới
    $this->db->bind(':min_order', $data['min_order_value']);
    $this->db->bind(':active', $data['is_active']);
    
    return $this->db->execute();
}

    // 6. Cập nhật trạng thái (Bật/Tắt)
    public function updateStatus($id, $status) {
        $sql = "UPDATE discounts 
                SET is_active = :status 
                WHERE discount_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // 7. Xóa mã giảm giá
    public function deleteDiscount($id) {
        $sql = "DELETE FROM discounts WHERE discount_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
}