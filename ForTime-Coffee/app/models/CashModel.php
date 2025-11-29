<?php
/*
 * CASH MODEL
 * Vai trò: Quản lý Ca làm việc (Shift) và Dòng tiền
 * Chức năng:
 * 1. Mở/Chốt ca làm việc.
 * 2. Tính toán doanh thu trong ca.
 * 3. Truy xuất lịch sử ca và chi tiết món bán được.
 */
class CashModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // -------------------------------------------------------------------------
    // CÁC HÀM XỬ LÝ CA HIỆN TẠI (ACTIVE SESSION)
    // -------------------------------------------------------------------------

    // 1. Lấy thông tin ca đang hoạt động (Chưa có giờ kết thúc)
    public function getCurrentSession() {
        $sql = "SELECT * FROM cash_sessions 
                WHERE end_time IS NULL 
                ORDER BY start_time DESC 
                LIMIT 1";
        $this->db->query($sql);
        return $this->db->single();
    }

    // 2. Mở ca mới (Khai báo tiền đầu ca)
    // Tham số: $userId (Người mở), $openingCash (Tiền trong két)
    public function startSession($userId, $openingCash) {
        $sql = "INSERT INTO cash_sessions (user_id, opening_cash, start_time) 
                VALUES (:uid, :cash, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':cash', $openingCash);
        
        return $this->db->execute();
    }

    // 3. Tính tổng doanh thu của ca hiện tại (Real-time)
    // Logic: Chỉ cộng tiền các đơn hàng có trạng thái 'paid'
    public function getCurrentSessionSales($startTime) {
        $sql = "SELECT SUM(final_amount) as total 
                FROM orders 
                WHERE status = 'paid' 
                AND order_time >= :start_time";
        
        $this->db->query($sql);
        $this->db->bind(':start_time', $startTime);
        
        $row = $this->db->single();
        return $row->total ?? 0; // Nếu chưa có đơn nào thì trả về 0
    }

    // 4. Chốt ca và lưu báo cáo
    public function closeSession($sessionId, $userId, $totalSales, $actualCash, $note) {
        $sql = "UPDATE cash_sessions 
                SET end_time = NOW(), 
                    total_sales = :sales, 
                    close_user_id = :uid, 
                    actual_cash = :actual, 
                    note = :note 
                WHERE session_id = :sid";
        
        $this->db->query($sql);
        $this->db->bind(':sales', $totalSales);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':actual', $actualCash);
        $this->db->bind(':note', $note);
        $this->db->bind(':sid', $sessionId);
        
        return $this->db->execute();
    }

    // -------------------------------------------------------------------------
    // CÁC HÀM BÁO CÁO & LỊCH SỬ (HISTORY)
    // -------------------------------------------------------------------------

    // 5. Lấy danh sách 5 ca gần nhất đã đóng (Để hiển thị bảng lịch sử)
public function getClosedSessions($limit = null) {
        $sql = "SELECT cs.*, u.full_name 
                FROM cash_sessions cs
                JOIN users u ON cs.user_id = u.user_id
                WHERE cs.end_time IS NOT NULL 
                ORDER BY cs.end_time DESC";
        
        // Nếu có truyền limit thì mới giới hạn, không thì lấy hết
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
        }
        
        $this->db->query($sql);
        
        if ($limit !== null) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        }
        
        return $this->db->resultSet();
    }

    // 6. Lấy chi tiết các món đã bán trong khoảng thời gian của một ca
    // Dùng cho tính năng "Xem chi tiết" (Icon con mắt)
public function getItemsInSession($startTime, $endTime) {
        // [CẬP NHẬT] Thêm od.note vào SELECT và GROUP BY
        $sql = "SELECT p.product_name, 
                       od.note, 
                       SUM(od.quantity) as qty, 
                       SUM(od.quantity * od.unit_price) as subtotal
                FROM order_details od
                JOIN orders o ON od.order_id = o.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE o.status = 'paid' 
                AND o.order_time >= :start_time 
                AND o.order_time <= :end_time
                GROUP BY p.product_id, od.note  -- Group theo cả Note để tách dòng
                ORDER BY qty DESC";
        
        $this->db->query($sql);
        $this->db->bind(':start_time', $startTime);
        $this->db->bind(':end_time', $endTime);
        
        return $this->db->resultSet();
    }
}