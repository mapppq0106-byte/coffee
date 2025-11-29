<?php
/*
 * DATABASE CORE CLASS
 * Vai trò: Lớp bao đóng (Wrapper) cho PDO (PHP Data Objects)
 * Chức năng:
 * 1. Kết nối đến cơ sở dữ liệu MySQL.
 * 2. Chuẩn bị câu truy vấn (Prepare Statement).
 * 3. Gán giá trị (Bind Values) để chống SQL Injection.
 * 4. Trả về kết quả dưới dạng mảng Object hoặc một dòng dữ liệu.
 */
class Database {
    // Lấy thông tin cấu hình từ file config/config.php
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    // Các biến xử lý PDO
    private $dbh;  // Database Handler
    private $stmt; // Statement
    private $error;

    // Constructor: Tự động chạy khi khởi tạo class
    public function __construct() {
        // Cấu hình DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';
        
        // Tùy chọn cấu hình PDO
        $options = array(
            PDO::ATTR_PERSISTENT => true, // Giữ kết nối liên tục (tăng hiệu năng)
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // Báo lỗi dạng Exception
        );

        // Thử kết nối
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            // Thiết lập múi giờ cho MySQL (Đồng bộ với PHP)
            $this->dbh->exec("SET time_zone = '+07:00'");
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Dừng chương trình và báo lỗi ngay lập tức
            die("Lỗi kết nối Cơ sở dữ liệu: " . $this->error);
        }
    }

    // 1. Chuẩn bị câu truy vấn (Prepare Statement)
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // 2. Gán giá trị vào tham số (Bind Value)
    // Giúp bảo mật, chống SQL Injection
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // 3. Thực thi câu lệnh (Dùng cho INSERT, UPDATE, DELETE)
    public function execute() {
        return $this->stmt->execute();
    }

    // 4. Lấy nhiều dòng dữ liệu (Dùng cho SELECT * FROM...)
    // Trả về: Mảng các Object
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // 5. Lấy một dòng dữ liệu duy nhất (Dùng cho SELECT ... LIMIT 1)
    // Trả về: Một Object đơn lẻ
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // 6. Đếm số dòng bị ảnh hưởng (Dùng để kiểm tra có bao nhiêu dòng khớp lệnh)
    public function rowCount() {
        return $this->stmt->rowCount();
    }
}