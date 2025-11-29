-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 27, 2025 lúc 03:07 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `f_coffee`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cash_sessions`
--

CREATE TABLE `cash_sessions` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Người mở ca',
  `start_time` datetime DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL,
  `opening_cash` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Tiền đầu ca',
  `total_sales` decimal(10,2) DEFAULT 0.00 COMMENT 'Doanh thu trong ca',
  `close_user_id` int(11) DEFAULT NULL COMMENT 'Người chốt ca',
  `actual_cash` decimal(10,2) DEFAULT 0.00 COMMENT 'Tiền thực tế kiểm đếm',
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cash_sessions`
--

INSERT INTO `cash_sessions` (`session_id`, `user_id`, `start_time`, `end_time`, `opening_cash`, `total_sales`, `close_user_id`, `actual_cash`, `note`) VALUES
(1, 3, '2025-11-24 18:04:12', '2025-11-24 18:06:33', 2000000.00, 50000.00, 3, 2000000.00, 'Thiếu 50K Do Chi Tiền '),
(2, 3, '2025-11-24 18:07:33', '2025-11-24 18:11:10', 2000000.00, 0.00, 3, 2000000.00, ''),
(3, 5, '2025-11-24 18:17:54', '2025-11-24 18:19:35', 2000000.00, 50000.00, 5, 2050000.00, ''),
(4, 1, '2025-11-24 23:57:53', '2025-11-25 01:11:34', 2000000.00, 85000.00, 1, 2085000.00, ''),
(5, 1, '2025-11-25 01:15:41', '2025-11-25 12:53:09', 2000000.00, 309000.00, 3, 2309000.00, ''),
(6, 1, '2025-11-25 12:53:22', '2025-11-25 13:17:28', 2000000.00, 35000.00, 1, 2035000.00, ''),
(7, 3, '2025-11-25 13:17:53', '2025-11-25 16:29:02', 2000000.00, 92000.00, 1, 2082000.00, 'Thiếu 10k do quý thối nhầm'),
(8, 1, '2025-11-25 16:29:20', '2025-11-25 16:35:38', 2082000.00, 45000.00, 3, 2127000.00, 'Đủ'),
(9, 1, '2025-11-25 16:35:50', '2025-11-27 10:52:47', 2127000.00, 150000.00, 1, 2277000.00, ''),
(10, 1, '2025-11-27 10:52:58', '2025-11-27 12:00:42', 2000000.00, 90000.00, 1, 2090000.00, ''),
(11, 1, '2025-11-27 12:00:52', '2025-11-27 16:40:59', 2090000.00, 0.00, 1, 2050000.00, 'Thiếu 40k'),
(12, 3, '2025-11-27 16:41:11', NULL, 2050000.00, 0.00, NULL, 0.00, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `is_deleted`) VALUES
(1, 'Cà Phê', 0),
(2, 'Trà Trái Cây', 0),
(3, 'Đá Xay', 0),
(5, 'Món Nóng', 0),
(9, 'Trà Sữa', 0),
(16, 'Xả', 1),
(17, 'Topping', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `min_order_value` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `discounts`
--

INSERT INTO `discounts` (`discount_id`, `code`, `type`, `value`, `is_active`, `min_order_value`) VALUES
(1, 'SALE10', 'percentage', 10.00, 1, 0.00),
(2, 'GIAM20K', 'fixed', 20000.00, 1, 0.00),
(3, 'UUDAINHANVIEN', 'percentage', 20.00, 1, 0.00),
(5, 'UUDAI500K', 'fixed', 500000.00, 1, 1000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `order_time` datetime DEFAULT current_timestamp(),
  `status` enum('pending','paid','canceled') DEFAULT 'pending',
  `discount_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `table_id`, `order_time`, `status`, `discount_id`, `total_amount`, `final_amount`) VALUES
(52, 3, 9, '2025-11-19 20:20:34', 'paid', NULL, 50000.00, 50000.00),
(53, 3, 9, '2025-11-19 20:24:07', 'paid', NULL, 50000.00, 50000.00),
(54, 1, 1, '2025-11-19 20:57:50', 'paid', NULL, 50000.00, 50000.00),
(55, 1, 9, '2025-11-19 21:28:35', 'paid', NULL, 50000.00, 50000.00),
(56, 1, 10, '2025-11-19 21:28:40', 'paid', NULL, 129000.00, 129000.00),
(57, 1, 2, '2025-11-19 21:28:46', 'paid', NULL, 219000.00, 219000.00),
(58, 1, 8, '2025-11-19 21:28:51', 'paid', NULL, 274000.00, 274000.00),
(59, 1, 6, '2025-11-19 22:56:57', 'paid', NULL, 55000.00, 55000.00),
(60, 3, 8, '2025-11-19 22:57:48', 'paid', NULL, 274000.00, 274000.00),
(61, 1, 3, '2025-11-19 23:15:39', 'paid', NULL, 50000.00, 50000.00),
(62, 1, 9, '2025-11-19 23:27:29', 'paid', NULL, 45000.00, 45000.00),
(63, 1, 1, '2025-11-19 23:36:34', 'paid', NULL, 50000.00, 50000.00),
(67, 1, 1, '2025-11-20 12:53:49', 'paid', NULL, 25000.00, 25000.00),
(68, 1, 11, '2025-11-20 12:55:59', 'canceled', NULL, NULL, 0.00),
(69, 1, 3, '2025-11-20 12:57:29', 'canceled', NULL, NULL, 0.00),
(70, 1, 3, '2025-11-20 13:03:53', 'canceled', NULL, NULL, 0.00),
(71, 1, 3, '2025-11-20 13:04:12', 'canceled', NULL, NULL, 0.00),
(72, 1, 1, '2025-11-20 13:08:23', 'canceled', NULL, NULL, 0.00),
(73, 1, 6, '2025-11-20 13:08:49', 'paid', NULL, 50000.00, 50000.00),
(74, 1, 6, '2025-11-20 14:13:24', 'canceled', NULL, NULL, 0.00),
(75, 1, 2, '2025-11-20 14:13:31', 'canceled', NULL, NULL, 0.00),
(76, 1, 4, '2025-11-20 14:13:40', 'canceled', NULL, NULL, 0.00),
(77, 1, 5, '2025-11-20 14:18:45', 'canceled', NULL, NULL, 0.00),
(78, 1, 9, '2025-11-23 13:22:24', 'canceled', NULL, NULL, 0.00),
(79, 1, 6, '2025-11-23 13:45:00', 'paid', NULL, 29000.00, 29000.00),
(80, 1, 9, '2025-11-23 14:02:33', 'paid', NULL, 50000.00, 50000.00),
(81, 1, 11, '2025-11-23 15:06:26', 'canceled', NULL, NULL, 0.00),
(82, 1, 11, '2025-11-23 15:06:29', 'paid', NULL, 45000.00, 45000.00),
(83, 1, 8, '2025-11-23 15:24:50', 'paid', NULL, 870000.00, 870000.00),
(84, 1, 3, '2025-11-23 15:27:23', 'paid', NULL, 25000.00, 25000.00),
(85, 3, 11, '2025-11-23 19:26:44', 'canceled', NULL, NULL, 0.00),
(86, 3, 9, '2025-11-23 19:26:53', 'canceled', NULL, NULL, 0.00),
(87, 3, 4, '2025-11-23 19:26:59', 'canceled', NULL, NULL, 0.00),
(88, 3, 4, '2025-11-23 19:27:03', 'canceled', NULL, NULL, 0.00),
(89, 1, 6, '2025-11-23 19:45:54', 'canceled', NULL, NULL, 0.00),
(91, 1, 11, '2025-11-23 19:46:42', 'canceled', NULL, NULL, 0.00),
(92, 1, 10, '2025-11-23 19:53:49', 'paid', NULL, 25000.00, 25000.00),
(93, 1, 3, '2025-11-23 20:29:42', 'paid', 2, 25000.00, 5000.00),
(94, 1, 6, '2025-11-23 20:36:07', 'paid', 2, 45000.00, 25000.00),
(95, 1, 6, '2025-11-23 20:36:55', 'paid', 2, 50000.00, 30000.00),
(96, 1, 3, '2025-11-23 20:45:19', 'canceled', NULL, NULL, 0.00),
(97, 3, 3, '2025-11-23 20:47:47', 'paid', 2, 50000.00, 30000.00),
(98, 1, 3, '2025-11-23 21:04:36', 'canceled', NULL, NULL, 0.00),
(99, 1, 3, '2025-11-24 16:21:34', 'paid', NULL, 50000.00, 50000.00),
(100, 3, 9, '2025-11-24 16:22:30', 'paid', 2, 29000.00, 9000.00),
(101, 3, 6, '2025-11-24 18:04:42', 'paid', NULL, 50000.00, 50000.00),
(102, 5, 9, '2025-11-24 18:18:55', 'paid', NULL, 50000.00, 50000.00),
(103, 1, 3, '2025-11-25 00:26:41', 'canceled', NULL, NULL, 0.00),
(104, 1, 3, '2025-11-25 00:59:34', 'paid', NULL, 35000.00, 35000.00),
(105, 1, 6, '2025-11-25 01:04:18', 'paid', NULL, 50000.00, 50000.00),
(106, 1, 3, '2025-11-25 11:08:26', 'canceled', NULL, NULL, 0.00),
(107, 1, 6, '2025-11-25 11:53:40', 'paid', NULL, 309000.00, 309000.00),
(108, 1, 3, '2025-11-25 12:51:17', 'canceled', NULL, NULL, 0.00),
(109, 1, 10, '2025-11-25 13:08:23', 'canceled', NULL, NULL, 0.00),
(110, 1, 6, '2025-11-25 13:17:13', 'paid', NULL, 35000.00, 35000.00),
(111, 1, 12, '2025-11-25 16:25:46', 'paid', 3, 115000.00, 92000.00),
(112, 3, 6, '2025-11-25 16:35:21', 'paid', NULL, 45000.00, 45000.00),
(113, 1, 1, '2025-11-25 17:40:29', 'paid', NULL, 40000.00, 40000.00),
(114, 1, 2, '2025-11-26 19:10:35', 'canceled', NULL, NULL, 0.00),
(115, 1, 3, '2025-11-26 19:11:01', 'paid', NULL, 15000.00, 15000.00),
(116, 1, 3, '2025-11-26 20:36:25', 'canceled', NULL, NULL, 0.00),
(117, 1, 3, '2025-11-27 10:38:04', 'canceled', NULL, NULL, 0.00),
(118, 1, 6, '2025-11-27 10:51:39', 'paid', NULL, 95000.00, 95000.00),
(119, 1, 3, '2025-11-27 11:18:47', 'canceled', NULL, 0.00, 0.00),
(120, 1, 7, '2025-11-27 11:19:16', 'canceled', NULL, 0.00, 0.00),
(121, 1, 7, '2025-11-27 11:47:40', 'canceled', NULL, 0.00, 0.00),
(122, 1, 7, '2025-11-27 11:57:57', 'paid', NULL, 90000.00, 90000.00),
(123, 1, 3, '2025-11-27 12:04:45', 'canceled', NULL, 0.00, 0.00),
(124, 1, 6, '2025-11-27 17:31:09', 'canceled', NULL, 0.00, 0.00),
(125, 1, 6, '2025-11-27 17:36:09', 'canceled', NULL, 0.00, 0.00),
(126, 1, 6, '2025-11-27 17:37:26', 'canceled', NULL, 0.00, 0.00),
(127, 1, 3, '2025-11-27 17:41:34', 'canceled', NULL, 0.00, 0.00),
(128, 1, 6, '2025-11-27 17:41:42', 'canceled', NULL, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `note`) VALUES
(63, 52, 8, 1, 50000.00, NULL),
(64, 53, 8, 1, 50000.00, NULL),
(65, 54, 9, 1, 50000.00, NULL),
(66, 55, 8, 1, 50000.00, NULL),
(67, 56, 8, 1, 50000.00, NULL),
(68, 56, 9, 1, 50000.00, NULL),
(69, 56, 2, 1, 29000.00, NULL),
(70, 57, 9, 1, 50000.00, NULL),
(71, 57, 8, 1, 50000.00, NULL),
(72, 57, 2, 1, 29000.00, NULL),
(73, 57, 4, 1, 45000.00, NULL),
(74, 57, 5, 1, 45000.00, NULL),
(75, 58, 9, 1, 50000.00, NULL),
(76, 58, 8, 1, 50000.00, NULL),
(77, 58, 2, 1, 29000.00, NULL),
(78, 58, 4, 1, 45000.00, NULL),
(79, 58, 5, 1, 45000.00, NULL),
(80, 58, 6, 1, 55000.00, NULL),
(81, 59, 6, 1, 55000.00, NULL),
(82, 60, 2, 1, 29000.00, NULL),
(83, 60, 4, 1, 45000.00, NULL),
(84, 60, 5, 1, 45000.00, 'Ít Ngọt'),
(85, 60, 9, 1, 50000.00, NULL),
(86, 60, 8, 1, 50000.00, 'it sữa'),
(87, 60, 6, 1, 55000.00, 'Đậm matcha'),
(88, 61, 9, 1, 50000.00, NULL),
(89, 62, 5, 1, 45000.00, NULL),
(90, 63, 8, 1, 50000.00, NULL),
(96, 67, 21, 1, 25000.00, NULL),
(102, 73, 9, 1, 50000.00, 'Ít đá'),
(108, 79, 2, 1, 29000.00, 'Ít Đá'),
(109, 80, 8, 1, 50000.00, NULL),
(111, 82, 5, 1, 45000.00, NULL),
(112, 83, 2, 30, 29000.00, NULL),
(113, 84, 21, 1, 25000.00, NULL),
(121, 92, 21, 1, 25000.00, NULL),
(122, 93, 21, 1, 25000.00, NULL),
(123, 94, 4, 1, 45000.00, NULL),
(124, 95, 8, 1, 50000.00, NULL),
(126, 97, 9, 1, 50000.00, NULL),
(128, 99, 9, 1, 50000.00, NULL),
(129, 100, 2, 1, 29000.00, NULL),
(130, 101, 9, 1, 50000.00, NULL),
(131, 102, 8, 1, 50000.00, NULL),
(133, 104, 22, 1, 35000.00, 'Ít Ngọt'),
(134, 105, 9, 1, 50000.00, NULL),
(136, 107, 22, 1, 35000.00, NULL),
(137, 107, 9, 1, 50000.00, NULL),
(138, 107, 5, 1, 45000.00, NULL),
(139, 107, 6, 1, 55000.00, NULL),
(140, 107, 2, 1, 29000.00, NULL),
(141, 107, 4, 1, 45000.00, NULL),
(142, 107, 8, 1, 50000.00, NULL),
(145, 110, 22, 1, 35000.00, NULL),
(146, 111, 22, 1, 35000.00, NULL),
(147, 111, 21, 1, 25000.00, NULL),
(148, 111, 6, 1, 55000.00, NULL),
(149, 112, 4, 1, 45000.00, NULL),
(150, 113, 22, 1, 40000.00, NULL),
(152, 115, 23, 1, 15000.00, NULL),
(157, 118, 22, 1, 55000.00, 'Size L, Pudding trứng'),
(158, 118, 22, 1, 40000.00, 'Size M'),
(162, 122, 22, 1, 50000.00, 'Size M, Trân Châu Đen, ít đá'),
(163, 122, 22, 1, 40000.00, 'Size M');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `price`, `image`, `is_available`, `is_deleted`) VALUES
(2, 1, 'Cà Phê Đen Đá', 29000.00, '1763539610_CA-PHE-DEN-DA-SAI-GON.png', 1, 0),
(3, 1, 'Bạc Xỉu', 32000.00, NULL, 1, 1),
(4, 2, 'Trà Đào Cam Sả', 45000.00, '1763567765_xả.jpg', 1, 0),
(5, 2, 'Trà Vải Hoa Hồng', 45000.00, '1763567787_vaihh.jpg', 1, 0),
(6, 3, 'Matcha Đá Xay', 55000.00, '1763567808_mtdx.png', 1, 0),
(8, 1, 'Cà Phê Dừa', 50000.00, '1763468036_dua.jpg', 1, 0),
(9, 1, 'Bạc Xỉu Kem Muối', 50000.00, '1763556225_sua.png', 1, 0),
(10, 2, 'Trà Sữa MaChiaTo', 45000.00, '1763536367_machiato.jpg', 1, 1),
(11, 1, 'Cà Phê Đen Đá', 15000.00, '1763536396_CA-PHE-DEN-DA-SAI-GON.png', 1, 1),
(16, 1, 'dsa', 12333.00, '', 1, 1),
(17, 3, 'kk', 12345.00, '', 1, 1),
(18, 1, 'ts', 12345.00, '', 1, 1),
(19, 1, 'gfasd', 12567.00, '', 1, 1),
(20, 1, 'kkds', 12555.00, '', 1, 1),
(21, 5, 'Trà Gừng', 25000.00, '1763615491_gungnong.jpg', 1, 0),
(22, 9, 'Trà Sữa Kem Trứng Nướng', 40000.00, '1764006506_suatrungnuong.jpg', 1, 0),
(23, 16, 'Xả vv', 15000.00, '', 1, 1),
(24, 9, 'Trà Gừng', -555.00, '', 1, 1),
(25, 17, 'Trân Châu Đen', 10000.00, '1764219578_ttd.jpg', 1, 0),
(26, 17, 'vv', 120000.00, '', 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Staff');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shift_logs`
--

CREATE TABLE `shift_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `shift_logs`
--

INSERT INTO `shift_logs` (`log_id`, `user_id`, `login_time`, `logout_time`, `note`) VALUES
(1, 3, '2025-11-24 16:30:08', '2025-11-24 16:30:09', NULL),
(2, 1, '2025-11-24 16:30:13', '2025-11-24 16:30:41', NULL),
(3, 3, '2025-11-24 16:30:45', '2025-11-24 16:31:11', NULL),
(4, 1, '2025-11-24 16:31:16', '2025-11-24 16:53:58', NULL),
(5, 3, '2025-11-24 16:54:02', '2025-11-24 16:54:30', NULL),
(6, 1, '2025-11-24 16:54:35', '2025-11-24 18:03:46', NULL),
(7, 3, '2025-11-24 18:03:55', '2025-11-24 18:06:33', NULL),
(8, 1, '2025-11-24 18:06:38', '2025-11-24 18:07:11', NULL),
(9, 1, '2025-11-24 18:07:14', '2025-11-24 18:07:18', NULL),
(10, 3, '2025-11-24 18:07:21', '2025-11-24 18:11:10', NULL),
(11, 1, '2025-11-24 18:11:13', '2025-11-24 18:15:12', NULL),
(12, 1, '2025-11-24 18:15:16', '2025-11-24 18:16:40', NULL),
(13, 5, '2025-11-24 18:16:50', '2025-11-25 17:09:41', 'Hệ thống tự chốt do quên đăng xuất'),
(14, 3, '2025-11-24 18:17:34', '2025-11-25 10:38:28', 'Hệ thống tự chốt do quên đăng xuất'),
(15, 5, '2025-11-24 18:17:49', '2025-11-24 18:18:25', NULL),
(16, 5, '2025-11-24 18:18:40', '2025-11-24 18:18:59', NULL),
(17, 5, '2025-11-24 18:19:14', '2025-11-24 18:19:35', NULL),
(18, 1, '2025-11-24 18:19:39', '2025-11-24 18:27:37', NULL),
(19, 1, '2025-11-24 23:57:23', '2025-11-24 23:59:49', NULL),
(20, 5, '2025-11-24 23:59:58', '2025-11-25 00:00:15', NULL),
(21, 1, '2025-11-25 00:00:19', '2025-11-25 01:11:34', NULL),
(22, 1, '2025-11-25 01:12:17', '2025-11-25 10:18:30', 'Hệ thống tự chốt do quên đăng xuất'),
(23, 1, '2025-11-25 10:06:25', '2025-11-25 10:16:10', NULL),
(24, 1, '2025-11-25 10:18:30', '2025-11-25 10:26:24', NULL),
(25, 3, '2025-11-25 10:38:28', '2025-11-25 10:41:43', NULL),
(26, 1, '2025-11-25 10:41:47', '2025-11-25 12:40:59', NULL),
(27, 1, '2025-11-25 12:44:16', '2025-11-25 12:51:47', NULL),
(28, 1, '2025-11-25 12:52:16', '2025-11-25 12:52:32', NULL),
(29, 3, '2025-11-25 12:52:36', '2025-11-25 12:53:09', NULL),
(30, 1, '2025-11-25 12:53:13', '2025-11-25 13:17:28', NULL),
(31, 3, '2025-11-25 13:17:35', '2025-11-25 13:17:39', NULL),
(32, 3, '2025-11-25 13:17:45', '2025-11-25 16:34:38', 'Hệ thống tự chốt do quên đăng xuất'),
(33, 1, '2025-11-25 16:25:23', '2025-11-25 16:29:02', NULL),
(34, 1, '2025-11-25 16:29:08', '2025-11-25 16:34:31', NULL),
(35, 3, '2025-11-25 16:34:38', '2025-11-25 16:35:38', NULL),
(36, 1, '2025-11-25 16:35:42', '2025-11-25 17:09:33', NULL),
(37, 5, '2025-11-25 17:09:41', '2025-11-25 17:11:02', NULL),
(38, 1, '2025-11-25 17:11:07', '2025-11-25 17:35:56', NULL),
(39, 1, '2025-11-25 17:37:04', '2025-11-25 17:58:26', NULL),
(40, 3, '2025-11-25 17:58:30', '2025-11-26 20:40:11', 'Hệ thống tự chốt do quên đăng xuất'),
(41, 1, '2025-11-26 18:51:51', '2025-11-26 19:00:22', 'Hệ thống tự chốt do quên đăng xuất'),
(42, 1, '2025-11-26 19:00:22', '2025-11-26 20:39:50', NULL),
(43, 3, '2025-11-26 20:40:11', '2025-11-26 20:41:06', 'Hệ thống tự chốt do quên đăng xuất'),
(44, 1, '2025-11-26 20:40:19', '2025-11-26 20:41:03', NULL),
(45, 3, '2025-11-26 20:41:06', '2025-11-26 20:41:52', 'Hệ thống tự chốt do quên đăng xuất'),
(46, 1, '2025-11-26 20:41:18', '2025-11-26 20:41:48', NULL),
(47, 3, '2025-11-26 20:41:52', '2025-11-27 16:41:03', 'Hệ thống tự chốt do quên đăng xuất'),
(48, 1, '2025-11-26 20:42:11', '2025-11-26 20:42:49', NULL),
(49, 1, '2025-11-26 20:43:05', '2025-11-26 21:33:52', 'Hệ thống tự chốt do quên đăng xuất'),
(50, 1, '2025-11-26 21:33:52', '2025-11-27 10:36:38', 'Hệ thống tự chốt do quên đăng xuất'),
(51, 1, '2025-11-27 10:36:38', '2025-11-27 10:52:47', NULL),
(52, 1, '2025-11-27 10:52:52', '2025-11-27 12:00:42', NULL),
(53, 1, '2025-11-27 12:00:46', '2025-11-27 12:06:57', NULL),
(54, 1, '2025-11-27 16:30:47', '2025-11-27 16:40:59', NULL),
(55, 3, '2025-11-27 16:41:03', '2025-11-27 17:04:19', NULL),
(56, 1, '2025-11-27 17:04:23', '2025-11-27 20:55:06', 'Hệ thống tự chốt do quên đăng xuất'),
(57, 1, '2025-11-27 20:55:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `status` enum('empty','occupied') DEFAULT 'empty',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tables`
--

INSERT INTO `tables` (`table_id`, `table_name`, `status`, `is_deleted`) VALUES
(1, 'Bàn 1', 'empty', 0),
(2, 'Bàn 2', 'empty', 0),
(3, 'Bàn 3', 'empty', 0),
(4, 'Bàn 4', 'empty', 0),
(5, 'Bàn 5', 'empty', 0),
(6, 'Bàn 6', 'empty', 0),
(7, 'Bàn 7', 'empty', 0),
(8, 'Bàn 8', 'empty', 0),
(9, 'Bàn 9', 'empty', 1),
(10, 'Bàn 10', 'empty', 0),
(11, 'Bàn 11', 'empty', 0),
(12, 'Bàn 12', 'empty', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `full_name`, `role_id`, `is_active`) VALUES
(1, 'admin', '$2y$10$Avvasc/fAUMM6a6DfhgLWOIFjT7qycD7IJFmBegWzwN0Y1yzsnIWm', 'Quản Trị Viên', 1, 1),
(3, 'vink56', '$2y$10$2Oew8wnAIgDYGVj5BUK2Cu5HqO7hTSJOE8ltGogMhLWhsmXfj5VoO', 'Trần Công Vink56', 2, 1),
(5, 'phuquy', '$2y$10$z6uYmchql.z2tzZrjkDe1.RIvXiOP8PBGG01zU6lDuMyJGBYxDgBi', 'Phan Phú Quý', 2, 1),
(6, 'lambaodoi', '$2y$10$X1N7LWmSBtNPI8KZXpW62eq8eVTfIk/.3/UJhhGfqHaUISZbf9Ay.', 'Hoài Lâm', 2, 1);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cash_sessions`
--
ALTER TABLE `cash_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Chỉ mục cho bảng `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `discount_id` (`discount_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Chỉ mục cho bảng `shift_logs`
--
ALTER TABLE `shift_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `table_name` (`table_name`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cash_sessions`
--
ALTER TABLE `cash_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `shift_logs`
--
ALTER TABLE `shift_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT cho bảng `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`table_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`discount_id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Các ràng buộc cho bảng `shift_logs`
--
ALTER TABLE `shift_logs`
  ADD CONSTRAINT `shift_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
