<?php
/*
 * TABLE MODEL
 * Vai trò: Quản lý Sơ đồ bàn (Table Map)
 * Chức năng: Thêm, Sửa, Xóa và Lấy danh sách bàn
 */
class TableModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // 1. Lấy danh sách tất cả các bàn (Sắp xếp theo ID tăng dần và chỉ lấy bàn chưa xóa )
  public function getTables() {
    $sql = "SELECT * FROM tables WHERE is_deleted = 0 ORDER BY table_id ASC";
    $this->db->query($sql);
    return $this->db->resultSet();
}

    // 2. Thêm bàn mới
    // Mặc định khi tạo mới, trạng thái là 'empty' (Trống)
    public function addTable($name) {
        $sql = "INSERT INTO tables (table_name, status) VALUES (:name, 'empty')";
        
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        
        return $this->db->execute();
    }

    // 3. Cập nhật tên bàn
    public function updateTable($id, $name) {
        $sql = "UPDATE tables SET table_name = :name WHERE table_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);
        
        return $this->db->execute();
    }

    // 4. Xóa bàn ( Xóa mềm )
  public function deleteTable($id) {
    $sql = "UPDATE tables SET is_deleted = 1, status = 'empty' WHERE table_id = :id";
    $this->db->query($sql);
    $this->db->bind(':id', $id);
    return $this->db->execute();
}
// [MỚI] Kiểm tra tên bàn đã tồn tại chưa
public function checkTableNameExists($name, $excludeId = null) {
        // Chỉ kiểm tra các bàn chưa bị xóa (is_deleted = 0)
        $sql = "SELECT table_id FROM tables WHERE table_name = :name AND is_deleted = 0";
        
        // Nếu đang sửa, loại trừ chính ID của bàn đó ra
        if ($excludeId) {
            $sql .= " AND table_id != :id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        
        $this->db->single();
        
        return $this->db->rowCount() > 0;
    }
}