-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026 at 10:22 AM
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `views` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `category_id`, `title`, `summary`, `content`, `image`, `created_at`, `views`, `status`) VALUES
(1, 1, 'Đánh giá các dòng smartphone mới nhất năm nay', 'Những dòng điện thoại cao cấp sở hữu chip xử lý mạnh mẽ và thời lượng pin sử dụng cả ngày.', 'Thị trường điện thoại thông minh năm nay tập trung vào các tính năng trí tuệ nhân tạo tích hợp trực tiếp trên máy. Ngoài ra, công nghệ tản nhiệt buồng hơi mới giúp máy luôn mát mẻ khi chơi game hoặc quay phim chất lượng cao.', 'DienThoai.jpg', '2026-10-16 20:30:51', 120, 1),
(2, 2, 'Xu hướng laptop mỏng nhẹ dành cho học sinh, sinh viên', 'Các mẫu máy tính xách tay phân khúc tầm trung đáp ứng tốt nhu cầu học tập và lập trình.', 'Với trọng lượng chỉ khoảng 1.3kg cùng thời lượng pin lên đến 10 tiếng, thế hệ laptop mới mang lại sự tiện lợi tối đa cho người dùng thường xuyên phải di chuyển.', 'XuHuongLapTopHSSV.jpg', '2026-10-16 20:30:51', 95, 1),
(3, 3, 'Ứng dụng thực tế của công nghệ mới trong đời sống', 'Các giải pháp tự động hóa giúp tối ưu hóa hiệu suất làm việc và giải phóng sức lao động.', 'Từ việc quản lý nhà thông minh, điều khiển giao thông đến hỗ trợ y tế từ xa, công nghệ đang ngày càng hiện diện rõ nét và nâng cao chất lượng cuộc sống của con người.', 'CongNgheDoiSong.jpg', '2026-10-16 20:30:51', 210, 1),
(4, 4, 'Khám phá vũ trụ bao la qua kính viễn vọng không gian', 'Những hình ảnh sắc nét về các cụm thiên hà cách xa Trái Đất hàng triệu năm ánh sáng.', 'Kính viễn vọng thế hệ mới vừa gửi về những dữ liệu quý giá về các hành tinh ngoài hệ mặt trời, mở ra cơ hội lớn cho việc tìm kiếm những hành tinh có điều kiện sống tương tự Trái Đất.', 'KhamPhaVuTru.jpg', '2026-10-16 20:30:51', 45, 1),
(5, 1, 'Công nghệ pin mới giúp kéo dài thời gian sử dụng thiết bị', 'Phát minh đột phá về vật liệu giúp sạc siêu nhanh và tăng tuổi thọ pin gấp nhiều lần.\r\nCác nhà khoa học phát triển công nghệ pin lithium-kim loại mới, hứa hẹn mang đến thời lượng sử dụng dài hơn và tốc độ sạc cực nhanh.', 'Theo tạp chí danh tiếng Advanced Materials, một nhóm nhà nghiên cứu tại Đại học Quốc gia Chonnam (Hàn Quốc) vừa phát triển thành công công nghệ pin lithium-kim loại mới hứa hẹn mang lại thời gian sử dụng kéo dài nhiều ngày cho smartphone và khả năng sạc đầy chỉ trong vài phút.\r\n\r\nCông nghệ pin mới giúp điện thoại dùng nhiều ngày, sạc đầy chỉ trong vài phút\r\nMột loại điện cực mới có thể khắc phục được vấn đề pin lithium\r\n\r\nPin lithium-kim loại là công nghệ pin sử dụng hóa học tương tự như pin lithium-ion truyền thống, nhưng thay thế than chì trong điện cực âm bằng lithium nguyên chất. Kết quả là mật độ năng lượng được tăng lên gấp 10 lần so với pin lithium-ion thông thường, đồng thời giảm trọng lượng và tăng tốc độ sạc.\r\n\r\nTuy nhiên, việc sử dụng lithium nguyên chất cũng đặt ra thách thức lớn, với sự hình thành của các nhánh tinh thể (dendrite). Những cấu trúc này có thể gây đoản mạch, làm thủng màng ngăn và dẫn đến hiện tượng quá nhiệt, gây cháy nổ.\r\n\r\nCông nghệ pin mới giúp điện thoại dùng nhiều ngày, sạc đầy chỉ trong vài phút\r\nSự hình thành các nhánh tinh thể trong pin lithium-kim loại\r\n\r\nBước đột phá trong công nghệ pin lithium-kim loại\r\nGiờ đây, nhóm nghiên cứu do Giáo sư Mincheol Chang của Đại học Quốc gia Chonnam dẫn đầu đã phát triển một chất điện phân rắn composite ba lớp mới giúp ngăn chặn sự hình thành các nhánh tinh thể. Chất điện phân này kết hợp các chất độn gốm với một chất đồng trùng hợp linh hoạt, cải thiện độ dẫn ion và độ bền cơ học.\r\n\r\nCấu trúc ba lớp của chất điện phân bao gồm hai lớp ngoài mềm hơn để tăng cường tiếp xúc với các kênh lithium, một lớp xương sống bên trong cứng chắc. Thiết kế này không chỉ cải thiện độ dẫn điện mà còn ngăn chặn sự hình thành các nhánh tinh thể.\r\n\r\nCông nghệ pin mới giúp điện thoại dùng nhiều ngày, sạc đầy chỉ trong vài phút\r\nĐiện thoại pin dung lượng lớn sạc đầy trong vài phút có thể sớm trở thành hiện thực\r\n\r\nTrong các thử nghiệm, nhóm nghiên cứu đã đạt được dung lượng 133,6 mAh/g với khả năng giữ dung lượng trên 80% sau 1.000 chu kỳ sạc/xả. Con số này tương đương với một viên pin 5.000 mAh chỉ với 37,5 gram vật liệu hoạt tính. Với trọng lượng của một viên pin smartphone thông thường từ 65 đến 80 gram, chúng ta có thể hướng tới những viên pin 10.000 mAh có khả năng sạc đầy chỉ trong vài phút.\r\n\r\nBáo cáo cho biết thêm, viên pin thử nghiệm vẫn hoạt động hiệu quả ngay cả khi bị gập đôi hoặc cắt một phần, từ đó mở ra nhiều cơ hội ứng dụng cho công nghệ này trong các thiết bị đeo được như nhẫn thông minh, kính thông minh và vòng đeo tay theo dõi sức khỏe.\r\n\r\nQuan trọng nhất, nghiên cứu không chỉ mang lại hy vọng cho tương lai của pin lithium-kim loại mà còn có thể cách mạng hóa ngành công nghiệp điện tử và xe điện.', 'CongNghePinMoi.jpg', '2026-10-16 20:30:51', 180, 1),
(8, 3, 'Dùng Claude Opus để ‘mổ xẻ’ TV LG cũ, lập trình viên tăng tốc ứng dụng gấp 10 lần', 'Trong khi LG không công khai bộ công cụ phát triển ứng dụng cho TV của mình, lập trình viên này phải dùng đến AI Claude để xây dựng bộ ứng dụng mới cho thiết bị.', 'Trên chiếc TV LG đời 2019, ứng dụng Plex chính thức mất khoảng 30 giây mới hiện màn hình chọn hồ sơ người dùng. Một lập trình viên đã dùng Claude Opus cùng các công cụ phân tích phần mềm để tự xây dựng ứng dụng thay thế, rút thời gian chờ xuống còn 3 giây.\r\n\r\nPlex là nền tảng giúp người dùng quản lý và xem phim, chương trình truyền hình từ thư viện nội dung cá nhân trên nhiều thiết bị, trong đó có TV thông minh. Thay vì phải tìm kiếm tệp video trực tiếp trên máy tính hoặc ổ lưu trữ, người dùng có thể duyệt thư viện, xem thông tin phim và phát nội dung qua giao diện Plex. Tuy nhiên, trên chiếc TV LG đời 2019 của lập trình viên này, ứng dụng chính thức hoạt động chậm đến mức mỗi thao tác đều trở nên mất kiên nhẫn.\r\n\r\nỨng dụng Plex chính thức mất khoảng 30 giây để tải màn hình chọn hồ sơ, thêm khoảng 30 giây để hiện giao diện chính. Ngay cả việc mở menu cũng mất khoảng 5 giây, trong khi trang thông tin phim cần khoảng 10 giây. Giao diện có cảm giác giống một trang web hơn là ứng dụng chạy trực tiếp trên TV.\r\n\r\nLG không cung cấp bộ công cụ phát triển ứng dụng native công khai cho lập trình viên thông thường. Để tìm hiểu cách phần mềm vận hành, tác giả đã root chiếc TV phục vụ nghiên cứu, trích xuất các thư viện hệ thống rồi phân tích chúng bằng Ghidra, công cụ thường được dùng để dịch ngược phần mềm.\r\n\r\nClaude Opus hỗ trợ lần theo địa chỉ trong mã máy, các tham chiếu chéo và chuỗi ký tự để giúp tác giả hiểu cấu trúc chương trình nhanh hơn. Tuy nhiên, AI đôi khi đưa ra kết luận sai, nên các giả thuyết vẫn phải được kiểm chứng bằng mã nhị phân và thử nghiệm trên thiết bị thật. Cộng đồng Homebrew của webOS cũng cung cấp tài liệu hữu ích cho quá trình phát triển.\r\n\r\nViệc root chỉ phục vụ nghiên cứu, không phải điều kiện để chạy ứng dụng mới. Sản phẩm hoàn thiện, mang tên PlxNative, có thể cài đặt mà không cần root TV.\r\n\r\nTừ thử nghiệm đơn giản đến ứng dụng hoàn chỉnh\r\n\r\nBan đầu, tác giả chỉ tạo một giao diện gồm các ô màu để kiểm tra khả năng dựng hình ở tốc độ 60 khung hình/giây. Các ô này cùng thử phát một tập phim The Office, qua đó giúp lập trình viên kiểm tra khả năng phối hợp giữa giao diện và hệ thống phát video. Sau đó, công cụ Fable hỗ trợ kết nối các thành phần trong hệ thống media để hình ảnh đầu tiên xuất hiện trên màn hình.\r\n\r\nQuá trình phát triển cũng bộc lộ những lỗi khó phát hiện. Một lỗi tràn ngăn xếp xuất phát từ phiên bản SDL do LG chỉnh sửa: cấu trúc dữ liệu mà thư viện ghi ra lớn hơn kích thước mà phần khai báo trong mã nguồn dự kiến, dẫn đến ghi đè bộ nhớ.\r\n\r\nĐể giảm rủi ro từ những lỗi quản lý bộ nhớ tương tự, tác giả chuyển phần lớn ứng dụng từ C sang Rust, ngôn ngữ có cơ chế kiểm tra an toàn bộ nhớ ngay từ quá trình biên dịch. Phần giao tiếp với các thành phần native của LG vẫn được giữ bằng C.\r\n\r\nTác giả tiếp tục xây dựng giao diện OpenGL riêng và tối ưu để duy trì tốc độ 60 khung hình/giây. Hiệu ứng đổ bóng ban đầu khiến hiệu năng giảm mạnh, buộc anh phải điều chỉnh shader, giảm độ phân giải của một số vùng đệm và lưu lại những kết quả có thể tái sử dụng.\r\n\r\nCác chỉ số theo dõi GPU Mali cho thấy bộ xử lý đồ họa bị giới hạn chủ yếu bởi khối lượng tính toán, thay vì chỉ bởi việc xử lý điểm ảnh. Phát hiện này giúp tác giả tập trung tối ưu đúng phần gây nghẽn.\r\n\r\n\r\n\r\nKết quả, PlxNative khởi động đến màn hình chọn hồ sơ trong khoảng 3 giây. Nếu ghi nhớ hồ sơ đã sử dụng, ứng dụng có thể đi thẳng đến giao diện chính sau khoảng 4 giây, thay vì chờ gần một phút như khi dùng Plex chính thức.\r\n\r\nỨng dụng hỗ trợ TV chạy webOS 4.0 trở lên, có thể phát trực tiếp nhiều định dạng như H.264, HEVC và video 4K khi phần cứng hỗ trợ. Những định dạng không tương thích vẫn có thể được máy chủ Plex chuyển mã. Người dùng có thể cài đặt qua LG Developer Mode hoặc kênh Homebrew của webOS.\r\n\r\nDự án cho thấy AI có thể giúp rút ngắn quá trình phân tích phần mềm và phát triển ứng dụng, nhưng không thể thay thế việc kiểm tra trên phần cứng thật. Tốc độ cải thiện đến từ sự kết hợp giữa công cụ AI, kỹ thuật dịch ngược và nhiều vòng thử nghiệm, tối ưu của lập trình viên. Đôi khi, chiếc TV cũ chưa hẳn đã quá chậm; vấn đề nằm ở phần mềm đang chạy trên nó.', '1792479480_claudeopus.jpg', '2026-10-20 13:58:00', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'editor',
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`, `failed_attempts`, `locked_until`) VALUES
(1, 'admin', '$2y$10$xoYFX1mFPxBSyxaRe3iIRutxkIWhxGShzEhjYUVd3qpCUKfJE1k7a', 'Quản Trị Viên', 'admin', 0, NULL),
(2, 'bientapvien', '$2y$10$xoYFX1mFPxBSyxaRe3iIRutxkIWhxGShzEhjYUVd3qpCUKfJE1k7a', 'Nguyễn Văn A', 'editor', 0, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
