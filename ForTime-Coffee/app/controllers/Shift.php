<?php
/*
 * SHIFT CONTROLLER
 * Chức năng: Quản lý ca làm việc (Mở ca, Chốt ca, Báo cáo tiền, Lịch sử ca)
 * Kết nối Model: app/models/CashModel.php
 * Kết nối View: app/views/shift/open.php, app/views/shift/report.php
 */
class Shift extends Controller {
    private $cashModel;

    public function __construct() {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) { 
            redirect('auth/login'); 
        }
        
        // 2. Load Model
        $this->cashModel = $this->model('CashModel');
    }

    // Chức năng: Trang báo cáo & Chốt ca hiện tại
    public function index() {
        // Lấy ca đang hoạt động
        $session = $this->cashModel->getCurrentSession();
        
        // Nếu chưa có ca -> Chuyển sang trang Khai báo đầu ca
        if (!$session) { 
            redirect('shift/open'); 
        }

        // Tính toán doanh thu hiện tại
        $currentSales = $this->cashModel->getCurrentSessionSales($session->start_time);
        $expectedCash = $session->opening_cash + $currentSales;

        // Lấy lịch sử 5 ca gần nhất để hiển thị
        $history = $this->cashModel->getClosedSessions();

        $data = [
            'session'  => $session,
            'sales'    => $currentSales,
            'expected' => $expectedCash,
            'history'  => $history
        ];

        $this->view('shift/report', $data);
    }

    // Chức năng: Trang khai báo tiền đầu ca (Mở ca)
    public function open() {
        // Nếu đã có ca đang mở -> Không cần mở nữa -> Về POS
        if ($this->cashModel->getCurrentSession()) {
            redirect('pos');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $money = $_POST['opening_cash'];
            
            // Tạo ca mới
            if ($this->cashModel->startSession($_SESSION['user_id'], $money)) {
                redirect('pos');
            }
        }

        $this->view('shift/open');
    }

    // Chức năng: Xử lý hành động Chốt ca & Đăng xuất
    public function close() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionId  = $_POST['session_id'];
            $actualCash = $_POST['actual_cash'];
            $note       = $_POST['note'];
            
            // Tính lại doanh thu lần cuối để chốt số liệu chính xác
            $session = $this->cashModel->getCurrentSession();
            $sales = $this->cashModel->getCurrentSessionSales($session->start_time);

            if ($this->cashModel->closeSession($sessionId, $_SESSION['user_id'], $sales, $actualCash, $note)) {
                // Chốt xong -> Tự động đăng xuất
                redirect('auth/logout');
            }
        }
    }

    // API Ajax: Lấy chi tiết các món đã bán trong một ca cụ thể
    // Được gọi khi bấm nút "Mắt" xem chi tiết ở bảng lịch sử
    public function get_session_details() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start = $_POST['start'];
            $end   = $_POST['end'];
            
            $items = $this->cashModel->getItemsInSession($start, $end);
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'items' => $items]);
            exit;
        }
    }
}