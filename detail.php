<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Tang luot xem them 1 truoc
    $update_views = mysqli_prepare($conn, "UPDATE posts SET views = views + 1 WHERE id = ?");
    mysqli_stmt_bind_param($update_views, "i", $id);
    mysqli_stmt_execute($update_views);
}

// Lay thong tin bai viet (se lay luon so luot xem moi nhat)
$stmt = mysqli_prepare($conn, "SELECT posts.*, categories.name AS cat_name 
                               FROM posts 
                               JOIN categories ON posts.category_id = categories.id 
                               WHERE posts.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    die("<div style='text-align:center; padding:40px;'><h3>Bài viết không tồn tại.</h3><a href='index.php'>Quay về trang chủ</a></div>");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - TechNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <div class="main-wrapper">
        <header class="site-header py-2">
            <div class="container d-flex justify-content-between align-items-center">
                <a href="index.php" class="site-logo">TECHNEWS<span>.vn</span></a>
                <a href="index.php" class="btn btn-sm btn-outline-dark">&larr; Trang chủ</a>
            </div>
        </header>

        <div class="container my-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 bg-white border p-4">
                    <div class="article-category mb-2"><?= htmlspecialchars($post['cat_name']) ?></div>
                    <h1 class="lead-article-title mb-2"><?= htmlspecialchars($post['title']) ?></h1>
                    <div class="small text-muted border-bottom pb-2 mb-3">
                        Xuất bản: <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
                    </div>

                    <p class="fw-bold text-secondary mb-3" style="font-size: 16px;">
                        <?= nl2br(htmlspecialchars($post['summary'])) ?>
                    </p>

                    <div class="mb-4 text-center">
                        <img src="uploads/<?= !empty($post['image']) ? $post['image'] : 'default.jpg' ?>" 
                             class="img-fluid border" alt="Ảnh minh họa" onerror="this.src='uploads/default.jpg'">
                    </div>

                    <div class="fs-6 lh-lg mb-4 text-dark" style="text-align: justify;">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>

                    <div class="border-top pt-3">
                        <a href="index.php" class="btn btn-sm btn-secondary">&larr; Quay lại danh sách tin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer py-3 text-center border-top">
        <div class="container small text-muted">
            Bản quyền &copy; 2026 TechNews.vn - Trang thông tin điện tử mô phỏng
        </div>
    </footer>

</body>
</html>