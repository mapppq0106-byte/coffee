<?php
/*
 * POS CONTROLLER
 * -------------------------------------------------------------------------
 * Chức năng: Xử lý màn hình bán hàng (Point of Sale)
 * - Giao diện chọn bàn, chọn món
 * - Các API Ajax: Thêm món, Sửa món (Upsize/Topping), Xóa món, Thanh toán...
 * Kết nối Model: TableModel, CategoryModel, ProductModel, OrderModel, CashModel, DiscountModel
 * Kết nối View: app/views/Pos/index.php
 * -------------------------------------------------------------------------
 */
class PosController extends Controller {
    private $tableModel;
    private $categoryModel;
    private $productModel;
    private $cashModel;

    public function __construct() {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('location: ' . URLROOT . '/auth/login');
            exit;
        }

        // 2. Kiểm tra CA LÀM VIỆC (Chỉ áp dụng cho Nhân viên)
        if ($_SESSION['role_id'] != 1) { 
            $this->cashModel = $this->model('CashModel'); 
            $activeSession = $this->cashModel->getCurrentSession();
            
            // Nếu chưa mở ca -> Chuyển sang trang khai báo
            if (!$activeSession) {
                header('location: ' . URLROOT . '/shift/open');
                exit;
            }
        }

        // 3. Load các Model cần thiết
        $this->tableModel = $this->model('TableModel');
        $this->categoryModel = $this->model('CategoryModel');
        $this->productModel = $this->model('ProductModel');
    }

    // Chức năng: Hiển thị giao diện bán hàng chính
    public function index() {
        $tables = $this->tableModel->getTables();
        $categories = $this->categoryModel->getCategories();
        $products = $this->productModel->getProducts();
        
        // [QUAN TRỌNG] Lấy danh sách Topping để hiển thị trong Modal sửa món
        $toppings = $this->productModel->getToppings();

        $data = [
            'tables' => $tables,
            'categories' => $categories,
            'products' => $products,
            'toppings' => $toppings
        ];

        $this->view('pos/index', $data);
    }

    // ============================================================
    // CÁC API AJAX (TRẢ VỀ JSON)
    // ============================================================

    // API: Lấy thông tin chi tiết đơn hàng của bàn (để vẽ lại bill)
    public function getTableOrder($tableId) {
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getUnpaidOrder($tableId); 
        
        if ($order) {
            $items = $orderModel->getOrderDetails($order->order_id);
            
            // Tính toán tiền
            $total = $order->total_amount;
            $discountAmount = 0;
            
            // Tính giảm giá (nếu có)
            if ($order->discount_id) {
                if ($order->discount_type == 'percentage') {
                    $discountAmount = $total * ($order->discount_value / 100);
                } else {
                    $discountAmount = $order->discount_value;
                }
            }
            
            $finalAmount = $total - $discountAmount;
            if ($finalAmount < 0) $finalAmount = 0;

            echo json_encode([
                'status' => 'success', 
                'order_id' => $order->order_id,
                'items' => $items,
                'total' => $total,
                'discount_amount' => $discountAmount,
                'discount_code' => $order->discount_code,
                'final_amount' => $finalAmount
            ]);
        } else {
            echo json_encode(['status' => 'empty']);
        }
    }

    // API: Thêm món vào đơn (Mặc định Size M, không topping) - [ĐÃ SỬA LẠI]
    public function addToOrder() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tableId = $_POST['table_id'];
            $productId = $_POST['product_id'];
            $price = $_POST['price']; // Giá gốc
            $userId = $_SESSION['user_id'];

            // Mặc định Note khi thêm nhanh
            $defaultNote = 'Size M'; 

            $orderModel = $this->model('OrderModel');
            $order = $orderModel->getUnpaidOrder($tableId);
            
            $isNewOrder = false;

            // Nếu chưa có đơn -> Tạo mới
            if (!$order) {
                $orderId = $orderModel->createOrder($userId, $tableId);
                $isNewOrder = true;
            } else {
                $orderId = $order->order_id;
            }

            if ($orderId) {
                // Thêm món với giá gốc và note mặc định
                $orderModel->addNumItem($orderId, $productId, $price, $defaultNote);
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Đã thêm món',
                    'is_new_order' => $isNewOrder 
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi tạo đơn']);
            }
        }
    }

    // [API MỚI] Cập nhật món (Sau khi chọn Size/Topping từ Modal)
public function updateOrderItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $detailId = $_POST['detail_id'];
            $orderId = $_POST['order_id'];
            $basePrice = (int)$_POST['base_price'];
            $extraPrice = (int)$_POST['extra_price'];
            
            $size = $_POST['size'];
            $toppingList = $_POST['toppings']; 
            
            // [MỚI] Nhận ghi chú tùy chỉnh
            $customNote = isset($_POST['custom_note']) ? trim($_POST['custom_note']) : '';

            // Tính giá mới
            $finalPrice = $basePrice + $extraPrice;

            // Tạo chuỗi Note tổng hợp
            $noteParts = [];
            
            // 1. Size
            if ($size === 'L') {
                $noteParts[] = "Size L";
            } else {
                $noteParts[] = "Size M";
            }

            // 2. Topping
            if (!empty($toppingList)) {
                $noteParts[] = $toppingList;
            }
            
            // 3. [MỚI] Ghi chú tùy chỉnh (Ít đá, v.v...)
            if (!empty($customNote)) {
                $noteParts[] = $customNote;
            }
            
            // Nối lại: "Size L, Trân châu, Ít đá"
            $newNote = implode(', ', $noteParts);

            $orderModel = $this->model('OrderModel');
            if ($orderModel->updateOrderDetail($detailId, $orderId, $finalPrice, $newNote)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }

    // API: Xóa một món khỏi đơn
    public function deleteItem() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $detailId = $_POST['detail_id'];
            $orderId = $_POST['order_id'];
            
            $orderModel = $this->model('OrderModel');
            
            if ($orderModel->deleteOrderDetail($detailId, $orderId)) {
                // Kiểm tra xem đơn hàng còn tồn tại không
                $db = new Database();
                $db->query("SELECT status FROM ORDERS WHERE order_id = :oid");
                $db->bind(':oid', $orderId);
                $check = $db->single();

                $isEmpty = ($check->status == 'canceled');

                echo json_encode([
                    'status' => 'success',
                    'is_empty' => $isEmpty 
                ]);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }

    // API: Cập nhật ghi chú món (Giữ lại nếu muốn sửa nhanh ghi chú text)
    public function addNote() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $detailId = $_POST['detail_id'];
            $note = $_POST['note'];
            
            $orderModel = $this->model('OrderModel');
            if ($orderModel->updateNote($detailId, $note)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }

    // API: Chuyển bàn / Gộp bàn
    public function changeTable() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fromTableId = $_POST['from_table_id'];
            $toTableId = $_POST['to_table_id'];

            $orderModel = $this->model('OrderModel');
            if ($orderModel->moveTable($fromTableId, $toTableId)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi xử lý DB']);
            }
        }
    }

    // API: Áp dụng / Hủy mã giảm giá
    public function applyDiscount() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tableId = $_POST['table_id'];
            $code = trim($_POST['code']);
            
            $orderModel = $this->model('OrderModel');
            $discountModel = $this->model('DiscountModel');
            
            $order = $orderModel->getUnpaidOrder($tableId);
            if (!$order) {
                echo json_encode(['status' => 'error', 'message' => 'Bàn trống!']);
                return;
            }

            // Nếu mã rỗng -> Hủy mã
            if (empty($code)) {
                $orderModel->removeDiscount($order->order_id);
                echo json_encode(['status' => 'success', 'message' => 'Đã hủy mã giảm giá']);
                return;
            }

            // Tìm và áp dụng mã
            $discount = $discountModel->getDiscountByCode($code);
            if ($discount) {
                // Kiểm tra điều kiện đơn tối thiểu
                if (isset($discount->min_order_value) && $discount->min_order_value > 0) {
                    $currentTotal = $order->total_amount;
                    if ($currentTotal < $discount->min_order_value) {
                        echo json_encode([
                            'status' => 'error', 
                            'message' => 'Mã này chỉ áp dụng cho đơn từ ' . number_format($discount->min_order_value) . 'đ trở lên!'
                        ]);
                        return; 
                    }
                }

                $orderModel->applyDiscount($order->order_id, $discount->discount_id);
                echo json_encode(['status' => 'success', 'message' => 'Áp dụng thành công!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Mã không tồn tại hoặc hết hạn!']);
            }
        }
    }

    // API: Thanh toán (Checkout)
    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tableId = $_POST['table_id'];
            $orderModel = $this->model('OrderModel');
            $order = $orderModel->getUnpaidOrder($tableId);
            
            if ($order) {
                // Tính lại tiền lần cuối
                $total = $order->total_amount;
                $discountAmount = 0;
                if ($order->discount_id) {
                     if ($order->discount_type == 'percentage') {
                        $discountAmount = $total * ($order->discount_value / 100);
                    } else {
                        $discountAmount = $order->discount_value;
                    }
                }
                $finalAmount = $total - $discountAmount;
                if($finalAmount < 0) $finalAmount = 0;

                // Chốt đơn
                if ($orderModel->checkoutOrder($order->order_id, $finalAmount)) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Lỗi DB']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn']);
            }
        }
    }
    // [API MỚI] Tăng giảm số lượng
    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $detailId = $_POST['detail_id'];
            $action = $_POST['action']; // 'inc' hoặc 'dec'
            
            $orderModel = $this->model('OrderModel');
            
            if ($orderModel->updateItemQuantity($detailId, $action)) {
                // Kiểm tra xem đơn hàng còn món không (để xử lý giao diện bàn trống nếu xóa hết)
                // Nhưng để đơn giản, ta chỉ cần trả về success, JS sẽ load lại bill
                // Nếu bill rỗng, hàm loadOrderDetails đã có logic tự xử lý (reload trang)
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error']);
            }
        }
    }
}