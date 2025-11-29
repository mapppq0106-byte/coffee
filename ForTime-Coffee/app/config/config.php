<?php
/*
 * CONFIGURATION FILE
 * Vai trò: Chứa các hằng số cấu hình toàn cục cho hệ thống
 * Lưu ý: Thay đổi thông tin tại đây sẽ ảnh hưởng đến toàn bộ website
 */

// ===================================================================================
// 1. CẤU HÌNH CƠ SỞ DỮ LIỆU (DATABASE SETTINGS)
// ===================================================================================
define('DB_HOST', 'localhost');  // Địa chỉ máy chủ (Thường là localhost trên XAMPP/Laragon)
define('DB_USER', 'root');       // Tên đăng nhập MySQL (Mặc định XAMPP là 'root')
define('DB_PASS', '');           // Mật khẩu MySQL (Mặc định XAMPP để trống)
define('DB_NAME', 'f_coffee');   // Tên Database (Phải khớp chính xác với trong phpMyAdmin)

// ===================================================================================
// 2. CẤU HÌNH ĐƯỜNG DẪN (PATH SETTINGS)
// ===================================================================================

// Đường dẫn thư mục ứng dụng (Path Vật Lý trên ổ cứng)
// Dùng để require/include file PHP. Ví dụ: C:\xampp\htdocs\ForTime-Coffee\app
define('APPROOT', dirname(dirname(__FILE__)));

// Đường dẫn URL gốc (Đường dẫn trình duyệt)
// Dùng cho: link CSS, JS, ảnh, thẻ <a href="...">.
// LƯU Ý: Không thêm dấu gạch chéo '/' ở cuối.
define('URLROOT', 'http://localhost/ForTime-Coffee');

// ===================================================================================
// 3. THÔNG TIN WEBSITE (SITE INFO)
// ===================================================================================
define('SITENAME', 'ForTime Coffee Management'); // Tên hiển thị trên tiêu đề Tab trình duyệt
define('APPVERSION', '1.0.0');                   // Phiên bản ứng dụng (Dùng để quản lý cache file tĩnh)