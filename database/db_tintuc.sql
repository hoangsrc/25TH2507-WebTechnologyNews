-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 04:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_tintuc`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Điện thoại & Di động'),
(2, 'Máy tính & Laptop'),
(3, 'Trí tuệ nhân tạo'),
(4, 'Khoa học & Đời sống');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `category_id`, `title`, `summary`, `content`, `image`, `created_at`) VALUES
(1, 1, 'Đánh giá các dòng smartphone mới nhất năm nay', 'Những dòng điện thoại cao cấp sở hữu chip xử lý mạnh mẽ và thời lượng pin sử dụng cả ngày.', 'Thị trường điện thoại thông minh năm nay tập trung vào các tính năng trí tuệ nhân tạo tích hợp trực tiếp trên máy. Ngoài ra, công nghệ tản nhiệt buồng hơi mới giúp máy luôn mát mẻ khi chơi game hoặc quay phim chất lượng cao.', 'DienThoai.jpg', '2026-10-16 20:30:51'),
(2, 2, 'Xu hướng laptop mỏng nhẹ dành cho học sinh, sinh viên', 'Các mẫu máy tính xách tay phân khúc tầm trung đáp ứng tốt nhu cầu học tập và lập trình.', 'Với trọng lượng chỉ khoảng 1.3kg cùng thời lượng pin lên đến 10 tiếng, thế hệ laptop mới mang lại sự tiện lợi tối đa cho người dùng thường xuyên phải di chuyển.', 'XuHuongLapTopHSSV.jpg', '2026-10-16 20:30:51'),
(3, 3, 'Ứng dụng thực tế của công nghệ mới trong đời sống', 'Các giải pháp tự động hóa giúp tối ưu hóa hiệu suất làm việc và giải phóng sức lao động.', 'Từ việc quản lý nhà thông minh, điều khiển giao thông đến hỗ trợ y tế từ xa, công nghệ đang ngày càng hiện diện rõ nét và nâng cao chất lượng cuộc sống của con người.', 'CongNgheDoiSong.jpg', '2026-10-16 20:30:51'),
(4, 4, 'Khám phá vũ trụ bao la qua kính viễn vọng không gian', 'Những hình ảnh sắc nét về các cụm thiên hà cách xa Trái Đất hàng triệu năm ánh sáng.', 'Kính viễn vọng thế hệ mới vừa gửi về những dữ liệu quý giá về các hành tinh ngoài hệ mặt trời, mở ra cơ hội lớn cho việc tìm kiếm những hành tinh có điều kiện sống tương tự Trái Đất.', 'KhamPhaVuTru.jpg', '2026-10-16 20:30:51'),
(5, 1, 'Công nghệ pin mới giúp kéo dài thời gian sử dụng thiết bị', 'Phát minh đột phá về vật liệu giúp sạc siêu nhanh và tăng tuổi thọ pin gấp nhiều lần.', 'Nhờ việc ứng dụng vật liệu bán dẫn tiên tiến, tốc độ truyền năng lượng được tăng lên đáng kể mà không sinh ra lượng nhiệt lớn, đảm bảo an toàn tuyệt đối khi sử dụng.', 'CongNghePinMoi.jpg', '2026-10-16 20:30:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
