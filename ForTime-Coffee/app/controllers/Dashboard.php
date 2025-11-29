<?php
/*
 * DASHBOARD CONTROLLER
 * Chức năng: Trung tâm báo cáo thống kê (Doanh thu, Đơn hàng, Biểu đồ, Lịch sử)
 * Kết nối Model: DashboardModel, OrderModel
 * Kết nối View: admin/dashboard/index
 */
class Dashboard extends Controller {
    private $dashboardModel;
    private $orderModel;

    public function __construct() {
        // Bảo mật: Chỉ Admin mới được truy cập
        $this->restrictToAdmin();

        // Load các Model cần thiết
        $this->dashboardModel = $this->model('DashboardModel');
        $this->orderModel = $this->model('OrderModel');
    }

    // Chức năng: Hiển thị trang Dashboard chính
public function index() {
        // 1. LẤY THAM SỐ TỪ URL
        $fromDate = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01'); // Mặc định đầu tháng
        $toDate = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $filterType = isset($_GET['type']) ? $_GET['type'] : 'day'; // Mặc định xem theo ngày

        // 2. XỬ LÝ PHÂN TRANG & LỊCH SỬ ĐƠN (Giữ nguyên logic cũ)
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $totalOrders = $this->orderModel->countOrders($fromDate, $toDate, $search);
        $totalPages = ceil($totalOrders / $limit);
        $allOrders = $this->orderModel->getAllOrders($fromDate, $toDate, $limit, $offset, $search);

        // 3. THỐNG KÊ TỔNG QUAN (Cards)
        $revenueToday = $this->dashboardModel->getRevenueToday();
        $ordersToday  = $this->dashboardModel->getOrdersCountToday();
        $revenueMonth = $this->dashboardModel->getRevenueThisMonth();
        $topProducts  = $this->dashboardModel->getTopProductsByDateRange($fromDate, $toDate);

        // 4. XỬ LÝ BIỂU ĐỒ (LOGIC MỚI)
        $rawChartData = $this->dashboardModel->getRevenueChartData($fromDate, $toDate, $filterType);
        
        // Chuẩn bị cấu hình lặp thời gian (DatePeriod)
        $chartData = [];
        $start = new DateTime($fromDate);
        $end = new DateTime($toDate);
        
        if ($filterType == 'month') {
            // Nếu xem theo THÁNG
            $interval = new DateInterval('P1M'); // Mỗi bước nhảy 1 tháng
            $end->modify('first day of next month'); // Để bao gồm cả tháng cuối
            $formatKey = 'Y-m';
            $formatLabel = 'm/Y'; // Hiển thị trên biểu đồ: 11/2025
        } elseif ($filterType == 'year') {
            // Nếu xem theo NĂM
            $interval = new DateInterval('P1Y'); // Mỗi bước nhảy 1 năm
            $end->modify('+1 year');
            $formatKey = 'Y';
            $formatLabel = 'Y'; // Hiển thị: 2025
        } else {
            // Nếu xem theo NGÀY (Mặc định)
            $interval = new DateInterval('P1D'); // Mỗi bước nhảy 1 ngày
            $end->modify('+1 day');
            $formatKey = 'Y-m-d';
            $formatLabel = 'd/m'; // Hiển thị: 25/11
        }

        // Tạo khung xương dữ liệu (Điền số 0)
        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $dt) {
            $chartData[$dt->format($formatKey)] = 0;
        }

        // Điền dữ liệu thật từ DB vào khung xương
        foreach ($rawChartData as $row) {
            if (isset($chartData[$row->date_label])) {
                $chartData[$row->date_label] = (int)$row->total;
            }
        }

        // Tách Label và Value cho ChartJS
        $chartLabels = []; 
        $chartValues = [];
        foreach ($chartData as $key => $val) {
            // Format lại label cho đẹp mắt (VD: 2025-11 -> Thg 11/2025)
            $dateObj = DateTime::createFromFormat($formatKey, $key);
            if ($filterType == 'month') {
                $chartLabels[] = "Thg " . $dateObj->format('m/Y');
            } elseif ($filterType == 'year') {
                $chartLabels[] = "Năm " . $dateObj->format('Y');
            } else {
                $chartLabels[] = $dateObj->format('d/m');
            }
            $chartValues[] = $val;
        }

        // 5. GÓI DỮ LIỆU
        $data = [
            'revenue_today' => $revenueToday,
            'orders_today'  => $ordersToday,
            'revenue_month' => $revenueMonth,
            'orders'        => $allOrders,
            'top_products'  => $topProducts,
            'chart_labels'  => $chartLabels,
            'chart_values'  => $chartValues,
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
            'filter_type'   => $filterType, // Truyền loại lọc xuống View để active option
            'current_page'  => $page,
            'total_pages'   => $totalPages,
            'search_keyword'=> $search
        ];

        $this->view('admin/dashboard/index', $data);
    }

    // Chức năng: API Lấy chi tiết đơn hàng (Trả về JSON)
    // Được gọi bởi Ajax khi bấm nút "Mắt" xem chi tiết
    public function get_order_detail($id) {
        // Đảm bảo Model đã được load
        if (!isset($this->orderModel)) {
            $this->orderModel = $this->model('OrderModel');
        }
        
        $data = $this->orderModel->getOrderDetail($id);
        
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}