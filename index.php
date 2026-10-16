<?php
require_once 'db.php';

// Lay ngay gio tieng Viet
$days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
$current_date = $days[date('w')] . ', ' . date('d/m/Y');

// Xu ly tim kiem va loc theo danh muc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;

$where = [];
if ($search != '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where[] = "posts.title LIKE '%$safe_search%'";
}
if ($cat_id > 0) {
    $where[] = "posts.category_id = $cat_id";
}

$where_clause = "";
if (count($where) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $where);
}

// Phan trang
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$sql_count = "SELECT COUNT(*) AS total FROM posts $where_clause";
$res_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($res_count);
$total_records = $row_count['total'] ?? 0;

$total_pages = ceil($total_records / $limit);
$offset = ($page - 1) * $limit;

// Lay danh sach bai viet
$sql_posts = "SELECT posts.*, categories.name AS cat_name 
              FROM posts 
              JOIN categories ON posts.category_id = categories.id 
              $where_clause 
              ORDER BY posts.id DESC 
              LIMIT $offset, $limit";
$res_posts = mysqli_query($conn, $sql_posts);

// Lay 1 bai viet moi nhat de lam tin tieu diem tren dau
$sql_featured = "SELECT posts.*, categories.name AS cat_name 
                 FROM posts 
                 JOIN categories ON posts.category_id = categories.id 
                 ORDER BY posts.id DESC LIMIT 1";
$res_featured = mysqli_query($conn, $sql_featured);
$featured_post = mysqli_fetch_assoc($res_featured);

// Lay danh sach chuyen muc
$res_cats = mysqli_query($conn, "SELECT * FROM categories");
$categories_list = [];
if ($res_cats) {
    while ($c = mysqli_fetch_assoc($res_cats)) {
        $categories_list[] = $c;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechNews - Trang Thông Tin Công Nghệ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <div class="main-wrapper">
        <!-- Header -->
        <header class="top-header py-2">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="index.php" class="brand-logo me-3">TECH<span>NEWS</span></a>
                    <span class="header-date d-none d-md-inline"><?= $current_date ?></span>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <form action="index.php" method="GET" class="d-flex">
                        <?php if ($cat_id > 0): ?>
                            <input type="hidden" name="cat_id" value="<?= $cat_id ?>">
                        <?php endif; ?>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tin tức..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Tìm</button>
                        </div>
                    </form>
                    <a href="login.php" class="btn btn-outline-danger btn-sm text-nowrap">Đăng nhập</a>
                </div>
            </div>
        </header>

        <!-- Category Menu Bar -->
        <nav class="nav-category-bar mb-4">
            <div class="container">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link <?= ($cat_id == 0) ? 'active' : '' ?>" href="index.php">Trang chủ</a>
                    </li>
                    <?php foreach ($categories_list as $cat): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($cat_id == $cat['id']) ? 'active' : '' ?>" 
                               href="index.php?cat_id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>

        <!-- Main Body Content -->
        <div class="container mb-5">
            <?php if ($featured_post && empty($search) && $cat_id == 0 && $page == 1): ?>
                <!-- Tin Tieu Diem Noi Bat Tren Cung -->
                <div class="featured-box p-3 mb-4 shadow-sm">
                    <div class="row g-3">
                        <div class="col-lg-7">
                            <img src="uploads/<?= !empty($featured_post['image']) ? $featured_post['image'] : 'default.jpg' ?>" 
                                 class="featured-img rounded" alt="Tiêu điểm" onerror="this.src='uploads/default.jpg'">
                        </div>
                        <div class="col-lg-5 d-flex flex-column justify-content-center">
                            <span class="badge bg-danger mb-2 align-self-start">Tiêu điểm</span>
                            <h3 class="fw-bold mb-3">
                                <a href="detail.php?id=<?= $featured_post['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($featured_post['title']) ?>
                                </a>
                            </h3>
                            <p class="text-muted mb-4"><?= htmlspecialchars($featured_post['summary']) ?></p>
                            <div>
                                <small class="text-muted d-block mb-3"><?= date('d/m/Y H:i', strtotime($featured_post['created_at'])) ?></small>
                                <a href="detail.php?id=<?= $featured_post['id'] ?>" class="btn btn-primary btn-sm px-3">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Danh sach tin tuc chinh -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="fw-bold text-uppercase m-0" style="color: #0056b3;">
                            <?= !empty($search) ? 'Kết quả tìm kiếm' : 'Tin tức mới cập nhật' ?>
                        </h5>
                        <small class="text-muted"><?= $total_records ?> bài viết</small>
                    </div>

                    <div class="row g-3">
                        <?php if ($res_posts && mysqli_num_rows($res_posts) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($res_posts)): ?>
                                <div class="col-md-6">
                                    <div class="card h-100 post-card shadow-sm border-0">
                                        <img src="uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" 
                                             class="card-img-top" alt="Hình ảnh" onerror="this.src='uploads/default.jpg'">
                                        <div class="card-body d-flex flex-column">
                                            <span class="badge bg-light text-secondary border mb-2 align-self-start">
                                                <?= htmlspecialchars($row['cat_name']) ?>
                                            </span>
                                            <h6 class="card-title fw-bold">
                                                <a href="detail.php?id=<?= $row['id'] ?>" class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($row['title']) ?>
                                                </a>
                                            </h6>
                                            <p class="card-text text-muted small flex-grow-1">
                                                <?= htmlspecialchars($row['summary']) ?>
                                            </p>
                                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><?= date('d/m/Y', strtotime($row['created_at'])) ?></small>
                                                <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Đọc</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 py-5 text-center bg-white rounded border">
                                <p class="text-muted mb-0">Không có bài viết nào phù hợp.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Phan trang -->
                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&cat_id=<?= $cat_id ?>">Trước</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="index.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&cat_id=<?= $cat_id ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&cat_id=<?= $cat_id ?>">Sau</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>

                <!-- Cot phai: Sidebar tin tuc bo sung -->
                <div class="col-lg-4">
                    <!-- Danh muc -->
                    <div class="sidebar-box mb-4 shadow-sm">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-uppercase">Chuyên mục chính</h6>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($categories_list as $cat): ?>
                                <li class="py-1 border-bottom d-flex justify-content-between align-items-center">
                                    <a href="index.php?cat_id=<?= $cat['id'] ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                    <span class="text-muted">&rsaquo;</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Thong tin lien he toa soan -->
                    <div class="sidebar-box shadow-sm">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-uppercase">Thông tin tòa soạn</h6>
                        <p class="small text-muted mb-2">Đường dây nóng đóng góp tin bài công nghệ:</p>
                        <p class="fw-bold text-danger fs-5 mb-2">1900 xxxx</p>
                        <p class="small text-muted mb-0">
                            Email: contact@technews.vn<br>
                            Hệ thống tổng hợp và cập nhật tin tức công nghệ trực tuyến.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-thanhnien pt-4 pb-3">
        <div class="container">
            <!-- chuyen muc -->
            <div class="row row-cols-2 row-cols-md-4 g-3 mb-4 pb-3 border-bottom">
                <div>
                    <div class="footer-title">CÔNG NGHỆ</div>
                    <ul class="list-unstyled small">
                        <li><a href="index.php?cat_id=1">Điện thoại</a></li>
                        <li><a href="index.php?cat_id=2">Máy tính</a></li>
                        <li><a href="index.php?cat_id=3">Trí tuệ nhân tạo</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-title">KHOA HỌC</div>
                    <ul class="list-unstyled small">
                        <li><a href="index.php?cat_id=4">Khám phá</a></li>
                        <li><a href="index.php">Vũ trụ</a></li>
                        <li><a href="index.php">Môi trường số</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-title">ĐỜI SỐNG SỐ</div>
                    <ul class="list-unstyled small">
                        <li><a href="index.php">Ứng dụng di động</a></li>
                        <li><a href="index.php">Bảo mật thông tin</a></li>
                        <li><a href="index.php">Thủ thuật công nghệ</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-title">HỆ THỐNG</div>
                    <ul class="list-unstyled small">
                        <li><a href="login.php">Đăng nhập quản trị</a></li>
                        <li><a href="index.php">Điều khoản sử dụng</a></li>
                        <li><a href="index.php">Chính sách bảo mật</a></li>
                    </ul>
                </div>
            </div>

            <!-- Thong tin ban quyen -->
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <span class="brand-logo fs-4">TECH<span>NEWS</span></span>
                    <p class="small text-muted mt-2 mb-0">
                        Trang tin tức điện tử tổng hợp công nghệ<br>
                        Email: toasoan@technews.vn - Hotline: 1900 xxxx
                    </p>
                </div>
                <div class="col-md-6 text-md-end small text-muted">
                    <p class="mb-1">Website phục vụ mục đích học tập và nghiên cứu | &copy; 2026 TechNews</p>
                    <p class="mb-0">Đề tài tiểu luận: Phát Triển Phần Mềm Mã Nguồn Mở | Sinh viên thực hiện: MSSV - 25TH2507 </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>