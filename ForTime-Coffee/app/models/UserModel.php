<?php
/*
 * USER MODEL
 * Vai trò: Quản lý Tài khoản (Nhân viên/Admin) và Chấm công (Shift)
 * Chức năng:
 * 1. Xác thực đăng nhập (Login).
 * 2. CRUD tài khoản người dùng.
 * 3. Quản lý vào/ra ca (Check-in/Check-out).
 * 4. Thống kê hiệu suất nhân viên (KPI).
 */
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // =========================================================================
    // 1. XÁC THỰC & ĐĂNG NHẬP (AUTHENTICATION)
    // =========================================================================

    // Xử lý đăng nhập
    public function login($username, $password) {
        $row = $this->findUserByUsername($username);
        
        // Kiểm tra user có tồn tại không
        if ($row == false) return false;
        
        // Kiểm tra mật khẩu (Hash)
        if (password_verify($password, $row->password_hash)) {
            // Kiểm tra trạng thái hoạt động (Tránh user bị khóa đăng nhập)
            if ($row->is_active == 0) {
                return false; 
            }
            return $row;
        }
        return false;
    }

    // Tìm user theo tên đăng nhập
    public function findUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username";
        $this->db->query($sql);
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    // Tìm user theo ID
    public function findUserById($id) {
        $sql = "SELECT * FROM users WHERE user_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Đổi mật khẩu
    public function changePassword($id, $newPassHash) {
        $sql = "UPDATE users SET password_hash = :pass WHERE user_id = :id";
        $this->db->query($sql);
        $this->db->bind(':pass', $newPassHash);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // =========================================================================
    // 2. QUẢN LÝ TÀI KHOẢN (CRUD USER)
    // =========================================================================

    // Lấy danh sách tất cả nhân viên (Kèm tên chức vụ)
    public function getAllUsers() {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.role_id 
                ORDER BY u.user_id DESC";
        
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // Thêm nhân viên mới
    public function addUser($data) {
        $sql = "INSERT INTO users (username, password_hash, full_name, role_id, is_active) 
                VALUES (:user, :pass, :name, :role, :active)";
        
        $this->db->query($sql);
        $this->db->bind(':user', $data['username']);
        $this->db->bind(':pass', $data['password']);
        $this->db->bind(':name', $data['full_name']);
        $this->db->bind(':role', $data['role_id']);
        $this->db->bind(':active', $data['is_active']);

        return $this->db->execute();
    }

    // Cập nhật thông tin nhân viên
    public function updateUser($data) {
        if (!empty($data['password'])) {
            // Trường hợp có đổi mật khẩu
            $sql = "UPDATE users 
                    SET full_name = :name, 
                        role_id = :role, 
                        is_active = :active, 
                        password_hash = :pass 
                    WHERE user_id = :id";
            $this->db->query($sql);
            $this->db->bind(':pass', $data['password']);
        } else {
            // Trường hợp giữ nguyên mật khẩu cũ
            $sql = "UPDATE users 
                    SET full_name = :name, 
                        role_id = :role, 
                        is_active = :active 
                    WHERE user_id = :id";
            $this->db->query($sql);
        }

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['full_name']);
        $this->db->bind(':role', $data['role_id']);
        $this->db->bind(':active', $data['is_active']);

        return $this->db->execute();
    }

    // Xóa nhân viên
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE user_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // =========================================================================
    // 3. CHẤM CÔNG & CA LÀM VIỆC (SHIFT LOGS)
    // =========================================================================

    // Ghi nhận bắt đầu ca làm (Khi đăng nhập)
    public function startShift($userId) {
        // BƯỚC 1: Tự động dọn dẹp các ca "treo" (Ca cũ quên đăng xuất)
        $sqlCleanup = "UPDATE shift_logs 
                       SET logout_time = NOW(), 
                           note = 'Hệ thống tự chốt do quên đăng xuất' 
                       WHERE user_id = :uid AND logout_time IS NULL";
        $this->db->query($sqlCleanup);
        $this->db->bind(':uid', $userId);
        $this->db->execute();

        // BƯỚC 2: Tạo ca làm việc mới
        $sqlNew = "INSERT INTO shift_logs (user_id, login_time) VALUES (:uid, NOW())";
        $this->db->query($sqlNew);
        $this->db->bind(':uid', $userId);
        
        return $this->db->execute();
    }

    // Ghi nhận kết thúc ca làm (Khi đăng xuất)
    public function endShift($userId) {
        $sql = "UPDATE shift_logs 
                SET logout_time = NOW() 
                WHERE user_id = :uid AND logout_time IS NULL 
                ORDER BY login_time DESC LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        return $this->db->execute();
    }

    // Lấy lịch sử chấm công cá nhân (10 lần gần nhất)
    public function getShiftHistory($userId) {
        $sql = "SELECT *, TIMEDIFF(logout_time, login_time) as duration 
                FROM shift_logs 
                WHERE user_id = :uid 
                ORDER BY login_time DESC 
                LIMIT 10";
        
        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        return $this->db->resultSet();
    }

    // Lấy chi tiết chấm công theo khoảng thời gian (Cho Admin xem)
    public function getShiftsByDate($userId, $fromDate, $toDate) {
        $sql = "SELECT *, TIMEDIFF(logout_time, login_time) as duration 
                FROM shift_logs 
                WHERE user_id = :uid 
                AND DATE(login_time) BETWEEN :from AND :to
                ORDER BY login_time DESC";
        
        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':from', $fromDate);
        $this->db->bind(':to', $toDate);
        return $this->db->resultSet();
    }

    // =========================================================================
    // 4. THỐNG KÊ HIỆU SUẤT (KPI)
    // =========================================================================

    // Lấy danh sách hiệu suất của TOÀN BỘ nhân viên
    public function getStaffPerformance($fromDate, $toDate) {
        // 1. Lấy danh sách Staff đang hoạt động
        $this->db->query("SELECT user_id, full_name, username FROM users WHERE role_id = 2 AND is_active = 1");
        $staffs = $this->db->resultSet();

        $result = [];

        foreach($staffs as $staff) {
            // A. Tính tổng doanh thu & số đơn
            $sqlOrder = "SELECT COUNT(*) as count, SUM(final_amount) as revenue 
                         FROM orders 
                         WHERE user_id = :uid AND status = 'paid' 
                         AND DATE(order_time) BETWEEN :from AND :to";
            $this->db->query($sqlOrder);
            $this->db->bind(':uid', $staff->user_id);
            $this->db->bind(':from', $fromDate);
            $this->db->bind(':to', $toDate);
            $orderStats = $this->db->single();

            // B. Tính tổng giờ làm (phút -> giờ)
            $sqlShift = "SELECT SUM(TIMESTAMPDIFF(MINUTE, login_time, logout_time)) as total_minutes 
                         FROM shift_logs 
                         WHERE user_id = :uid 
                         AND logout_time IS NOT NULL
                         AND DATE(login_time) BETWEEN :from AND :to";
            $this->db->query($sqlShift);
            $this->db->bind(':uid', $staff->user_id);
            $this->db->bind(':from', $fromDate);
            $this->db->bind(':to', $toDate);
            $shiftStats = $this->db->single();

            $hours = $shiftStats->total_minutes ? round($shiftStats->total_minutes / 60, 1) : 0;

            $result[] = [
                'info' => $staff,
                'total_orders' => $orderStats->count ?? 0,
                'total_revenue' => $orderStats->revenue ?? 0,
                'total_hours' => $hours
            ];
        }
        
        // Sắp xếp: Người có doanh thu cao nhất lên đầu
        usort($result, function($a, $b) {
            return $b['total_revenue'] - $a['total_revenue'];
        });

        return $result;
    }

    // Lấy hiệu suất của MỘT nhân viên cụ thể
    public function getStaffStatsById($userId, $fromDate, $toDate) {
        // Doanh thu & Đơn hàng
        $sqlOrder = "SELECT COUNT(*) as count, SUM(final_amount) as revenue 
                     FROM orders 
                     WHERE user_id = :uid AND status = 'paid' 
                     AND DATE(order_time) BETWEEN :from AND :to";
        $this->db->query($sqlOrder);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':from', $fromDate);
        $this->db->bind(':to', $toDate);
        $orderStats = $this->db->single();

        // Giờ làm
        $sqlShift = "SELECT SUM(TIMESTAMPDIFF(MINUTE, login_time, logout_time)) as total_minutes 
                     FROM shift_logs 
                     WHERE user_id = :uid 
                     AND logout_time IS NOT NULL
                     AND DATE(login_time) BETWEEN :from AND :to";
        $this->db->query($sqlShift);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':from', $fromDate);
        $this->db->bind(':to', $toDate);
        $shiftStats = $this->db->single();

        return [
            'orders' => $orderStats->count ?? 0,
            'revenue' => $orderStats->revenue ?? 0,
            'hours' => $shiftStats->total_minutes ? round($shiftStats->total_minutes / 60, 1) : 0
        ];
    }
}