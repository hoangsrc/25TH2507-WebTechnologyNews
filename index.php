<?php
require_once 'db.php';

// Lay ngay tieng Viet
$weekday_map = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
$today_text = $weekday_map[date('w')] . ', ' . date('d/m/Y');

// Loc theo tu khoa va chuyen muc
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;

// Bat buoc: Chi hien thi bai viet DA DUOC DUYET (status = 1) ra trang chu
$where = ["posts.status = 1"];

if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $where[] = "posts.title LIKE '%$safe_search%'";
}
if ($cat_id > 0) {
    $where[] = "posts.category_id = $cat_id";
}

$where_clause = "WHERE " . implode(" AND ", $where);

// Phan trang an toan
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$sql_count = "SELECT COUNT(*) AS total FROM posts $where_clause";
$res_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($res_count);
$total_records = $row_count['total'] ?? 0;

$total_pages = ceil($total_records / $limit);
if ($total_pages < 1) $total_pages = 1;

if ($page > $total_pages) {
    $page = $total_pages;
}

$offset = ($page - 1) * $limit;

// Lay danh sach tin chinh da duyet
$sql_posts = "SELECT posts.*, categories.name AS cat_name 
              FROM posts 
              JOIN categories ON posts.category_id = categories.id 
              $where_clause 
              ORDER BY posts.id DESC 
              LIMIT $offset, $limit";
$res_posts = mysqli_query($conn, $sql_posts);

// Lay bai tieu diem (chi hien o trang dau tien khi khong tim kiem)
$featured_post = null;
if (empty($search) && $cat_id == 0 && $page == 1) {
    $res_lead = mysqli_query($conn, "SELECT posts.*, categories.name AS cat_name 
                                    FROM posts 
                                    JOIN categories ON posts.category_id = categories.id 
                                    WHERE posts.status = 1
                                    ORDER BY posts.id DESC LIMIT 1");
    $featured_post = mysqli_fetch_assoc($res_lead);
}

// Lay danh sach chuyen muc
$res_cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
$all_cats = [];
if ($res_cats) {
    while ($c = mysqli_fetch_assoc($res_cats)) {
        $all_cats[] = $c;
    }
}

// Logic: Lay danh sach tin doc nhieu theo LUOT XEM THAT (views DESC)
$res_popular = mysqli_query($conn, "SELECT id, title, views FROM posts WHERE status = 1 ORDER BY views DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bản Tin Công Nghệ - TechNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body>

    <div class="main-wrapper">
        <!-- Header -->
        <header class="site-header py-2">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-baseline">
                    <a href="index.php" class="site-logo">TECHNEWS<span>.vn</span></a>
                    <span class="site-date d-none d-md-inline"><?= $today_text ?></span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <form action="index.php" method="GET" class="d-flex">
                        <?php if ($cat_id > 0): ?>
                            <input type="hidden" name="cat_id" value="<?= $cat_id ?>">
                        <?php endif; ?>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tin bài..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Tìm</button>
                        </div>
                    </form>
                    <a href="login.php" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center px-2 py-1" title="Đăng nhập quản trị">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
                    </a>
                </div>
            </div>
        </header>

        <!-- Navbar chuyen muc -->
        <nav class="category-navbar mb-3">
            <div class="container">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link <?= ($cat_id == 0) ? 'active' : '' ?>" href="index.php">Trang chủ</a>
                    </li>
                    <?php foreach ($all_cats as $cat): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($cat_id == $cat['id']) ? 'active' : '' ?>" 
                               href="index.php?cat_id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container mb-4">
            <!-- Tin tieu diem -->
            <?php if ($featured_post): ?>
                <div class="lead-article">
                    <div class="row g-3">
                        <div class="col-lg-7">
                            <img src="uploads/<?= !empty($featured_post['image']) ? $featured_post['image'] : 'default.jpg' ?>" 
                                 class="lead-img" alt="Tiêu điểm" onerror="this.src='uploads/default.jpg'">
                        </div>
                        <div class="col-lg-5 d-flex flex-column justify-content-center">
                            <div class="article-category mb-1"><?= htmlspecialchars($featured_post['cat_name']) ?></div>
                            <h2 class="lead-article-title mb-2">
                                <a href="detail.php?id=<?= $featured_post['id'] ?>">
                                    <?= htmlspecialchars($featured_post['title']) ?>
                                </a>
                            </h2>
                            <p class="text-secondary small mb-3"><?= htmlspecialchars($featured_post['summary']) ?></p>
                            <div class="small text-muted d-flex justify-content-between">
                                <span>Xuất bản: <?= date('d/m/Y H:i', strtotime($featured_post['created_at'])) ?></span>
                                <span>Lượt xem: <?= $featured_post['views'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Cot danh sach tin chinh -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-3">
                        <span class="fw-bold text-uppercase" style="color: var(--primary-red); font-size: 13px;">
                            <?= !empty($search) ? 'Kết quả tìm kiếm' : 'Tin tức mới nhất' ?>
                        </span>
                        <span class="text-muted small"><?= $total_records ?> bài viết</span>
                    </div>

                    <div class="row g-3">
                        <?php if ($res_posts && mysqli_num_rows($res_posts) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($res_posts)): ?>
                                <div class="col-md-6">
                                    <div class="article-item">
                                        <img src="uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" 
                                             class="article-thumb" alt="Hình ảnh" onerror="this.src='uploads/default.jpg'">
                                        <div class="article-body">
                                            <a href="index.php?cat_id=<?= $row['category_id'] ?>" class="article-category">
                                                <?= htmlspecialchars($row['cat_name']) ?>
                                            </a>
                                            <div class="article-title">
                                                <a href="detail.php?id=<?= $row['id'] ?>">
                                                    <?= htmlspecialchars($row['title']) ?>
                                                </a>
                                            </div>
                                            <div class="article-desc">
                                                <?= htmlspecialchars($row['summary']) ?>
                                            </div>
                                            <div class="article-meta d-flex justify-content-between">
                                                <span><?= date('d/m/Y', strtotime($row['created_at'])) ?></span>
                                                <span class="text-muted"><?= $row['views'] ?> lượt xem</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-4 bg-white border">
                                <p class="text-muted m-0">Không có bài viết nào trong mục này.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Phan trang -->
                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&cat_id=<?= $cat_id ?>">&laquo; Trước</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="index.php?page=<?= $i ?>&search=<?= urlencode($search) ?>&cat_id=<?= $cat_id ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="index.php?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&cat_id=<?= $cat_id ?>">Sau &raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>

                <!-- Cot phai: Tin doc nhieu xep theo views that -->
                <div class="col-lg-4">
                    <div class="sidebar-panel">
                        <div class="panel-header">Đọc nhiều nhất</div>
                        <?php $rank = 1; ?>
                        <?php while ($pop = mysqli_fetch_assoc($res_popular)): ?>
                            <div class="ranking-item">
                                <div class="ranking-num">0<?= $rank++ ?></div>
                                <div class="ranking-title">
                                    <a href="detail.php?id=<?= $pop['id'] ?>"><?= htmlspecialchars($pop['title']) ?></a>
                                    <small class="text-muted d-block"><?= $pop['views'] ?> lượt xem</small>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="sidebar-panel">
                        <div class="panel-header">Chuyên mục chính</div>
                        <ul class="list-unstyled m-0">
                            <?php foreach ($all_cats as $cat): ?>
                                <li class="py-1 border-bottom d-flex justify-content-between">
                                    <a href="index.php?cat_id=<?= $cat['id'] ?>" class="text-dark small text-decoration-none">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="sidebar-panel">
                        <div class="panel-header">Thông tin tòa soạn</div>
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
    
    <!-- Footer bao chi -->
    <footer class="site-footer pt-4 pb-3">
        <div class="container">
            <div class="row row-cols-2 row-cols-md-4 g-3 pb-3 mb-3 border-bottom">
                <div>
                    <div class="footer-section-title">Chuyên mục</div>
                    <ul class="list-unstyled small">
                        <li><a href="index.php?cat_id=1">Điện thoại di động</a></li>
                        <li><a href="index.php?cat_id=2">Máy tính xách tay</a></li>
                        <li><a href="index.php?cat_id=3">Trí tuệ nhân tạo</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-section-title">Khoa học</div>
                    <ul class="list-unstyled small">
                        <li><a href="index.php?cat_id=4">Khám phá công nghệ</a></li>
                        <li><a href="index.php">Thế giới số</a></li>
                        <li><a href="index.php">Ứng dụng đời sống</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-section-title">Vận hành</div>
                    <ul class="list-unstyled small">
                        <li><a href="login.php">Khu vực quản trị</a></li>
                        <li><a href="index.php">Điều khoản quy định</a></li>
                        <li><a href="index.php">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div>
                    <div class="footer-section-title">Tòa soạn</div>
                    <p class="small text-muted mb-0">
                        Hệ thống tin tức công nghệ trực tuyến.<br>
                        Email: toasoan@technews.vn<br>
                        Đường dây nóng: 1900 xxxx
                    </p>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center small text-muted">
                <div>
                    <strong>TECHNEWS.VN</strong> - Trang thông tin điện tử công nghệ  &copy; 2026 TechNews  
                </div>
                <div>
                    Website phục vụ mục đích học tập <span style="font-weight: bold; font-size: 1.2em;">|</span> Học phần: Phát Triển Phần Mềm Mã Nguồn Mở <span style="font-weight: bold; font-size: 1.2em;">|</span> Sinh viên thực hiện: MSSV - 25TH2507
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
