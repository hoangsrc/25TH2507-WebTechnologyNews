# TechNews - Website Tin tức Công nghệ

<p>
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white" alt="PHP 8.x">
  <img src="https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white" alt="MySQL / MariaDB">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5.3">
  <img src="https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/Platform-Windows-0078D6?logo=windows&logoColor=white" alt="Windows">
</p>

**TechNews** là website tin tức công nghệ kết hợp hệ thống quản trị tòa soạn điện tử. Nền tảng hỗ trợ độc giả theo dõi tin tức thực tế, cung cấp công cụ cho biên tập viên quản lý bài viết và cho phép quản trị viên kiểm duyệt nội dung trước khi xuất bản ra trang chủ.

---

## Tổng quan hệ thống

TechNews giải quyết bài toán quản trị nội dung tin tức theo quy trình tòa soạn có kiểm soát. Thay vì cho phép mọi bài viết xuất bản ngay sau khi lưu, hệ thống áp dụng cơ chế phân quyền dựa trên vai trò (RBAC) với quy trình bắt buộc: **Biên tập -> Chờ duyệt -> Phê duyệt xuất bản**.

### Công nghệ sử dụng

| Thành phần | Chi tiết công nghệ |
|---|---|
| Ngôn ngữ máy chủ | PHP 8.x thuần kết hợp thư viện mở rộng MySQLi |
| Hệ quản trị CSDL | MariaDB / MySQL 10.4.x (`db_tintuc`) |
| Giao diện người dùng | HTML5, CSS3 (`css/style.css`), Bootstrap 5.3 CDN |
| Môi trường vận hành | Máy chủ Apache và MySQL trên gói XAMPP (Windows 11) |
| Kiến trúc mã nguồn | Lập trình hướng thủ tục thuần, không sử dụng Framework bên ngoài |

---

## Tính năng chính

### Dành cho Độc giả (Khách vãng lai)
- **Danh mục tin tức:** Khám phá bài viết theo 4 chuyên mục chuyên biệt: Điện thoại & Di động, Máy tính & Laptop, Trí tuệ nhân tạo, Khoa học & Đời sống.
- **Thanh điều hướng dính (Sticky Navigation):** Menu luôn cố định tại đỉnh màn hình khi cuộn trang.
- **Thời gian tiếng Việt động:** Hiển thị thứ, ngày, tháng, năm tự động theo thời gian thực của máy chủ.
- **Bài tiêu điểm & Lưới tin:** Bố cục bài viết chính nổi bật đi kèm lưới tin tức 2 cột chuẩn giao diện báo chí.
- **Bảng xếp hạng "Đọc nhiều nhất":** Tự động truy vấn và sắp xếp các bài viết dựa trên số lượt xem thực tế (`ORDER BY views DESC LIMIT 5`).
- **Lượt xem thực tế:** Tự động tăng lượt xem nguyên tử (`views = views + 1`) mỗi khi mở xem chi tiết tại `detail.php`.
- **Phân trang an toàn (Safe Pagination):** Tự động xử lý và điều chỉnh các tham số GET không hợp lệ (`?page=-5`, `?page=abc`, số vượt quá tổng trang) về đúng phạm vi trang hợp lệ.

### Dành cho Biên tập viên (Editor)
- Đăng bài viết mới kèm hình ảnh đại diện.
- Chỉnh sửa nội dung bài viết đã có.
- **Cơ chế kiểm duyệt bắt buộc:** Mọi thao tác lưu bài viết mới hoặc sửa bài đã xuất bản từ Editor đều tự động chuyển về trạng thái **Chờ duyệt** (`status = 0`) và ẩn khỏi trang chủ.
- **Khóa quyền xóa:** Nút xóa bài viết bị vô hiệu hóa trên giao diện; tệp xử lý backend tự động chặn yêu cầu xóa trái phép.

### Dành cho Quản trị viên (Admin)
- Xem danh sách toàn bộ tin bài và duyệt các bài viết đang ở trạng thái chờ duyệt sang **Đã xuất bản** (`status = 1`).
- Quản trị tài khoản người dùng nội bộ: Tạo tài khoản, xóa tài khoản.
- **Nút Mở khóa 1-click:** Cho phép Admin mở khóa ngay lập tức cho các tài khoản bị khóa do nhập sai mật khẩu quá 5 lần.
- **Chống tự xóa chính mình:** Cơ chế bảo vệ ngăn chặn Admin đang đăng nhập tự xóa tài khoản của bản thân.

### Cơ chế bảo mật và quản lý tệp tin

| Hạng mục | Cơ chế xử lý kỹ thuật |
|---|---|
| Chống SQL Injection | Áp dụng 100% Prepared Statements (`mysqli_prepare`, `bind_param`) cho tất cả các câu truy vấn có dữ liệu đầu vào. |
| Bảo vệ mật khẩu | Mật khẩu được băm một chiều bằng Bcrypt thông qua hàm `password_hash()` và đối soát bằng `password_verify()`. |
| Chống dò mật khẩu (Brute-force) | Hệ thống đếm lỗi riêng cho từng username (`failed_attempts`); sai quá 5 lần sẽ khóa tài khoản trong 15 phút tại CSDL (`locked_until`). |
| Chống tấn công XSS | Dữ liệu động in ra HTML đều được xử lý qua hàm `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. |
| Kiểm soát ảnh tải lên | Giới hạn dung lượng tối đa 2MB; chỉ nhận đuôi `jpg`, `png`, `webp`; xác thực cấu trúc nhị phân ảnh thật bằng hàm `getimagesize()`. |
| Chống ghi đè tệp tin | Tự động đổi tên tệp ảnh tải lên theo mốc thời gian hệ thống `time()` kết hợp đuôi mở rộng. |
| Dọn dẹp bộ nhớ vật lý | Sử dụng hàm `unlink()` để xóa vĩnh viễn tệp ảnh cũ trong thư mục `uploads/` khi xóa bài viết, đồng thời có điều kiện bảo vệ không xóa ảnh `default.jpg`. |

---

## Quy trình kiểm duyệt bài viết

```text
[Editor soạn / sửa bài] ──> [Lưu vào CSDL] ──> [Trạng thái Chờ duyệt: status = 0]
│
                                                       (Ẩn khỏi Trang chủ)
                                                                 │
                                                                 ▼
[Trang chủ hiển thị] <── [Đã xuất bản: status = 1] <── [Admin phê duyệt bài]
```
## Cấu trúc thư mục mã nguồn

```
25TH2507-WebTechnologyNews/
├── css/
│   └── style.css            # Tệp định dạng riêng (tông màu đỏ bầm #990000, Sticky Nav)
├── database/
│   └── db_tintuc.sql        # Kịch bản khởi tạo CSDL và dữ liệu mẫu có khóa ngoại
├── uploads/                 # Thư mục chứa ảnh đại diện bài viết và ảnh default.jpg
├── db.php                   # Kết nối cơ sở dữ liệu MySQLi và thiết lập utf8mb4
├── index.php                # Giao diện trang chủ người đọc, phân trang an toàn
├── detail.php               # Trang xem chi tiết tin tức, tự tăng lượt xem
├── login.php                # Đăng nhập bảo mật, chống brute-force, ghi nhớ Remember Me
├── logout.php               # Hủy phiên làm việc session
├── admin.php                # Khu vực quản trị bài viết và phê duyệt tin bài
├── add_post.php             # Form thêm bài viết, kiểm tra ảnh thật bằng getimagesize()
├── edit_post.php            # Form chỉnh sửa bài viết, tự động hạ về chờ duyệt
├── delete_post.php          # Xử lý xóa bài viết và gọi unlink() xóa ảnh vật lý
├── manage_users.php         # Quản trị danh sách người dùng, mở khóa 1-click
└── add_user.php             # Form tạo người dùng nội bộ, kiểm soát Regex Unicode
```

## Hướng dẫn cài đặt và chạy thử trên Localhost
### 1. Yêu cầu môi trường

- Hệ điều hành Windows 10/11.

- Đã cài đặt XAMPP (khuyến nghị phiên bản PHP 8.x).

- Đã cài đặt Git hoặc GitHub Desktop.
### 2. Tải mã nguồn về máy
Sao chép kho lưu trữ vào đúng thư mục `htdocs` của XAMPP:

- **Cách 1: Sử dụng Git CLI**
```bash
cd C:\xampp\htdocs
git clone https://github.com/hoangsrc/25TH2507-WebTechnologyNews.git
```

- **Cách 2: Sử dụng GitHub Desktop**
  - Nhấn `File` $\rightarrow$ `Clone repository...` (hoặc phím tắt `Ctrl + Shift + O`).
  - Mục URL nhập: `https://github.com/hoangsrc/25TH2507-WebTechnologyNews.git`
  - Mục Local Path trỏ đến thư mục: `C:\xampp\htdocs`
  - Nhấn `Clone`. Thư mục sau khi tải về sẽ nằm tại: `C:\xampp\htdocs\25TH2507-WebTechnologyNews`

### 3. Khởi động dịch vụ XAMPP

1. Mở ứng dụng **XAMPP Control Panel**.
2. Nhấn nút **Start** tại module **Apache**.
3. Nhấn nút **Start** tại module **MySQL**.
4. Đảm bảo cả hai cổng hoạt động bình thường (mặc định Port 80, 443 cho Apache và Port 3306 cho MySQL).

### 4. Nhập cơ sở dữ liệu mẫu

1. Mở trình duyệt web và truy cập vào công cụ phpMyAdmin: http://localhost/phpmyadmin.
2. Tại cột bên trái, chọn **New** để tạo cơ sở dữ liệu mới:
   - Database name: `db_tintuc`
   - Collation: chọn `utf8mb4_unicode_ci`
   - Bấm nút **Create**.
3. Chọn cơ sở dữ liệu `db_tintuc` vừa tạo, chuyển sang tab **Import** (Nhập).
4. Tại mục *File to import*, bấm **Choose File** (Chọn tệp) và tìm đến tệp:
   `C:\xampp\htdocs\25TH2507-WebTechnologyNews\database\db_tintuc.sql`
5. Cuộn xuống cuối trang và bấm nút **Import** để hệ thống tự động thiết lập các bảng và nạp dữ liệu mẫu.

### 5. Kiểm tra kết nối cơ sở dữ liệu

Mở tệp `db.php` để đối soát cấu hình kết nối. Mặc định trên XAMPP:

```php
$conn = mysqli_connect('localhost', 'root', '', 'db_tintuc');
mysqli_set_charset($conn, 'utf8mb4');
```

*(Nếu MySQL của bạn có đặt mật khẩu riêng cho tài khoản root, vui lòng điền mật khẩu đó vào tham số thứ 3).*

### 6. Khởi chạy ứng dụng

Mở trình duyệt và truy cập các đường dẫn tương ứng:

*   **Giao diện trang chủ tin tức:** http://localhost/25TH2507-WebTechnologyNews/index.php
*   **Khu vực đăng nhập quản trị:** http://localhost/25TH2507-WebTechnologyNews/login.php

### Tài khoản thử nghiệm (Default Credentials)

| Vai trò | Tên đăng nhập | Mật khẩu | Quyền hạn chính |
| :--- | :--- | :--- | :--- |
| Quản trị viên<br>(Admin) | `admin` | `12345` | Toàn quyền hệ thống, phê duyệt bài viết, quản lý và mở khóa tài khoản người dùng, xóa tin bài. |
| Biên tập viên<br>(Editor) | `bientapvien` | `12345` | Thêm bài viết mới, chỉnh sửa nội dung bài viết. Bài viết luôn ở trạng thái chờ Admin duyệt. |

> **Lưu ý bảo mật:** Các tài khoản trên là dữ liệu mẫu phục vụ kiểm thử cục bộ. Khi đưa vào môi trường thực tế, mật khẩu cần được thay đổi theo chính sách bảo mật nâng cao.