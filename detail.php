<?php
require_once 'db.php';

// Lay id bai viet tu duong dan url
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Su dung Prepared Statement de bao mat
$stmt = mysqli_prepare($conn, "SELECT posts.*, categories.name AS cat_name 
                               FROM posts 
                               JOIN categories ON posts.category_id = categories.id 
                               WHERE posts.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    die("<div style='text-align:center; padding:50px;'><h3>Bai viet khong ton tai!</h3><a href='index.php'>Quay lai trang chu</a></div>");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper">
        <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">TechNews</a>
                <a href="index.php" class="btn btn-outline-light btn-sm">&larr; Quay lại trang chủ</a>
            </div>
        </nav>

        <!-- Noi dung chi tiet -->
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($post['cat_name']) ?></span>
                    <h2 class="fw-bold mb-3"><?= htmlspecialchars($post['title']) ?></h2>
                    <p class="text-muted border-bottom pb-2">
                        Ngày đăng: <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                    </p>

                    <img src="uploads/<?= !empty($post['image']) ? $post['image'] : 'default.jpg' ?>" 
                         class="post-detail-img mb-4 shadow-sm" alt="ình ảnh"
                         onerror="this.src='uploads/CongNgheDoiSong.jpg'">

                    <div class="lead fw-normal text-secondary mb-4">
                        <?= nl2br(htmlspecialchars($post['summary'])) ?>
                    </div>

                    <div class="fs-6 lh-lg mb-5 text-dark">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>

                    <div class="border-top pt-3">
                        <a href="index.php" class="btn btn-secondary">&larr; Quay lại danh sách tin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <small>Hệ thống quản lý tin tức công nghệ &copy; 2026</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>