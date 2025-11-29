<?php
/*
 * APP CORE CLASS
 * Vai trò: Router & Dispatcher (Bộ định tuyến & Điều phối)
 * Chức năng:
 * 1. Lấy URL từ thanh địa chỉ.
 * 2. Phân tích URL theo cấu trúc: domain.com/Controller/Method/Params
 * 3. Tự động khởi tạo Controller và gọi Method tương ứng.
 */
class App {
    // Cấu hình mặc định khi không có URL (Vào trang Đăng nhập)
    protected $currentController = 'AuthController'; 
    protected $currentMethod = 'login';              
    protected $params = [];

    public function __construct() {
        // 1. Lấy mảng URL đã phân tách
        $url = $this->getUrl();

        // 2. XỬ LÝ CONTROLLER (URL[0])
        if (isset($url[0])) {
            // Chuẩn hóa tên: viết hoa chữ cái đầu (vd: pos -> Pos)
            $u_name = ucwords($url[0]);
            
            // Kiểm tra file Controller tồn tại không?
            // Ưu tiên 1: Tên có đuôi 'Controller' (VD: PosController.php) -> Chuẩn mới
            if (file_exists('../app/controllers/' . $u_name . 'Controller.php')) {
                $this->currentController = $u_name . 'Controller';
                
                // QUAN TRỌNG: Khi chuyển sang Controller mới, reset method về mặc định 'index'
                // Tránh trường hợp giữ method 'login' của AuthController
                $this->currentMethod = 'index'; 
                
                unset($url[0]);
            } 
            // Ưu tiên 2: Tên ngắn (VD: Dashboard.php) -> Hỗ trợ code cũ
            elseif (file_exists('../app/controllers/' . $u_name . '.php')) {
                $this->currentController = $u_name;
                $this->currentMethod = 'index';
                unset($url[0]);
            }
        }

        // Require và Khởi tạo Controller
        require_once '../app/controllers/' . $this->currentController . '.php';
        $this->currentController = new $this->currentController;

        // 3. XỬ LÝ METHOD (URL[1])
        if (isset($url[1])) {
            // Kiểm tra xem trong Controller đó có hàm này không
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // 4. XỬ LÝ THAM SỐ (PARAMS)
        // Lấy các phần còn lại của URL làm tham số (nếu có)
        $this->params = $url ? array_values($url) : [];

        // 5. GỌI HÀM CHÍNH THỨC (Callback)
        // Tương đương: $controller->$method($param1, $param2...)
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    // Hàm phụ: Lấy và xử lý URL
    public function getUrl() {
        if (isset($_GET['url'])) {
            // Loại bỏ khoảng trắng và ký tự lạ cuối chuỗi
            $url = rtrim($_GET['url'], '/');
            // Lọc ký tự không hợp lệ (Bảo mật)
            $url = filter_var($url, FILTER_SANITIZE_URL);
            // Tách chuỗi thành mảng dựa trên dấu gạch chéo "/"
            return explode('/', $url);
        }
        return [];
    }
}