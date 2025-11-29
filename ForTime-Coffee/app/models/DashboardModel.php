<?php
/*
 * DASHBOARD MODEL
 * Vai trò: Xử lý các truy vấn thống kê cho trang quản trị
 * Chức năng:
 * 1. Tính toán doanh thu, số lượng đơn hàng.
 * 2. Lấy dữ liệu vẽ biểu đồ (Chart).
 * 3. Thống kê top sản phẩm bán chạy.
 */
class DashboardModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // -------------------------------------------------------------------------
    // THỐNG KÊ TỔNG QUAN (CARDS)
    // -------------------------------------------------------------------------

    // 1. Tổng doanh thu hôm nay
    // Lưu ý: Chỉ tính đơn hàng đã thanh toán (status = 'paid')
    public function getRevenueToday() {
        $sql = "SELECT SUM(final_amount) as total 
                FROM orders 
                WHERE DATE(order_time) = CURDATE() 
                AND status = 'paid'";
        
        $this->db->query($sql);
        $row = $this->db->single();
        return $row->total ?? 0;
    }

    // 2. Số lượng đơn hàng hôm nay
    public function getOrdersCountToday() {
        $sql = "SELECT COUNT(*) as count 
                FROM orders 
                WHERE DATE(order_time) = CURDATE() 
                AND status = 'paid'";
        
        $this->db->query($sql);
        $row = $this->db->single();
        return $row->count ?? 0;
    }

    // 3. Tổng doanh thu tháng này
    public function getRevenueThisMonth() {
        // Sử dụng final_amount để tính doanh thu thực tế (đã trừ khuyến mãi)
        $sql = "SELECT SUM(final_amount) as total 
                FROM orders 
                WHERE MONTH(order_time) = MONTH(CURRENT_DATE()) 
                AND YEAR(order_time) = YEAR(CURRENT_DATE())
                AND status = 'paid'";
        
        $this->db->query($sql);
        $row = $this->db->single();
        return $row->total ?? 0;
    }

    // 4. Lấy 5 đơn hàng mới nhất để hiển thị widget
    public function getRecentOrders() {
        $sql = "SELECT * FROM orders 
                WHERE status = 'paid' 
                ORDER BY order_time DESC 
                LIMIT 5";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // -------------------------------------------------------------------------
    // DỮ LIỆU BIỂU ĐỒ & TOP SẢN PHẨM
    // -------------------------------------------------------------------------

    // 5. Lấy dữ liệu biểu đồ doanh thu theo khoảng thời gian tùy chọn
    // Tham số: $fromDate, $toDate (Định dạng YYYY-MM-DD)
// 5. Lấy dữ liệu biểu đồ linh động (Ngày / Tháng / Năm)
    public function getRevenueChartData($fromDate, $toDate, $type = 'day') {
        // Định dạng gom nhóm dữ liệu SQL
        switch ($type) {
            case 'month':
                // Gom theo Tháng (VD: 2025-11)
                $groupBy = "DATE_FORMAT(order_time, '%Y-%m')";
                break;
            case 'year':
                // Gom theo Năm (VD: 2025)
                $groupBy = "DATE_FORMAT(order_time, '%Y')";
                break;
            default: // 'day'
                // Gom theo Ngày (VD: 2025-11-25)
                $groupBy = "DATE(order_time)";
                break;
        }

        $sql = "SELECT $groupBy as date_label, SUM(final_amount) as total 
                FROM orders 
                WHERE status = 'paid' 
                AND DATE(order_time) BETWEEN :from AND :to
                GROUP BY date_label
                ORDER BY date_label ASC";
        
        $this->db->query($sql);
        $this->db->bind(':from', $fromDate);
        $this->db->bind(':to', $toDate);
        
        return $this->db->resultSet();
    }

    // 6. Top sản phẩm bán chạy theo khoảng thời gian
    public function getTopProductsByDateRange($fromDate, $toDate) {
        // Tính doanh thu từng món dựa trên chi tiết đơn hàng
        $sql = "SELECT p.product_name, SUM(od.quantity * od.unit_price) as revenue 
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                JOIN orders o ON od.order_id = o.order_id
                WHERE o.status = 'paid' 
                AND DATE(o.order_time) BETWEEN :from AND :to
                GROUP BY p.product_id 
                ORDER BY revenue DESC 
                LIMIT 5";
        
        $this->db->query($sql);
        $this->db->bind(':from', $fromDate);
        $this->db->bind(':to', $toDate);
        
        return $this->db->resultSet();
    }

    // 7. (Dự phòng) Biểu đồ doanh thu 7 ngày gần nhất
    // Giữ lại để tương thích ngược nếu cần
    public function getRevenueLast7Days() {
        $sql = "SELECT DATE(order_time) as date, SUM(final_amount) as total 
                FROM orders 
                WHERE status = 'paid' 
                AND order_time >= DATE(NOW()) - INTERVAL 6 DAY
                GROUP BY DATE(order_time)
                ORDER BY date ASC";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    // 8. (Dự phòng) Top món bán chạy toàn thời gian
    public function getTopProducts() {
        $sql = "SELECT p.product_name, SUM(od.quantity * od.unit_price) as revenue 
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                JOIN orders o ON od.order_id = o.order_id
                WHERE o.status = 'paid' 
                GROUP BY p.product_id 
                ORDER BY revenue DESC 
                LIMIT 4";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}