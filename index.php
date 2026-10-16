<?php
require_once 'db.php';

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

//danh muc cho menu
$res_cats = mysqli_query($conn, "SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Tin Tuc Cong Nghe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /*giao dien luon phu kin chieu cao man hinh*/
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-wrapper {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
        .card-img-top {
            height: 190px;
            object-fit: cover;
        }
        .post-card {
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body class="bg-light">

    <!-- Phien ban boc toan bo noi dung tren de day footer xuong duoi -->
    <div class="main-wrapper">
        <!-- Header / Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">TechNews</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= ($cat_id == 0) ? 'active' : '' ?>" href="index.php">Tat ca</a>
                        </li>
                        <?php if ($res_cats && mysqli_num_rows($res_cats) > 0): ?>
                            <?php while ($cat = mysqli_fetch_assoc($res_cats)): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?= ($cat_id == $cat['id']) ? 'active' : '' ?>" 
                                       href="index.php?cat_id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                                </li>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </ul>
                    <a href="login.php" class="btn btn-outline-light btn-sm">Quan tri</a>
                </div>
            </div>
        </nav>

        <!-- Thanh tim kiem -->
        <div class="bg-secondary text-white py-4 mb-4">
            <div class="container">
                <h3 class="mb-2">Tin Tuc Cong Nghe</h3>
                <p class="mb-3">Cap nhat tin tuc dien thoai, may tinh va cong nghe moi</p>
                <form action="index.php" method="GET" class="row g-2">
                    <?php if ($cat_id > 0): ?>
                        <input type="hidden" name="cat_id" value="<?= $cat_id ?>">
                    <?php endif; ?>
                    <div class="col-md-6 col-sm-8">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nhap tu khoa can tim..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <button type="submit" class="btn btn-primary w-100">Tim kiem</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sach bai viet -->
        <div class="container mb-5">
            <div class="row g-4">
                <?php if ($res_posts && mysqli_num_rows($res_posts) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($res_posts)): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 post-card">
                                <img src="uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" 
                                     class="card-img-top" alt="Hinh anh"
                                     onerror="this.src='uploads/default.jpg'">
                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-secondary mb-2 align-self-start"><?= htmlspecialchars($row['cat_name']) ?></span>
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
                                        <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-5">Không có bài viết nào phù hợp.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Phan trang -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&cat_id=<?= $cat_id ?>">Truoc</a>
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
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <small>He thong quan ly tin tuc cong nghe &copy; 2026</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>