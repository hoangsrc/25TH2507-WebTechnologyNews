<?php
session_start();
require_once 'db.php';

// Kiem tra session dang nhap
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Lay danh sach tat ca bai viet kem ten danh muc
$sql = "SELECT posts.*, categories.name AS cat_name 
        FROM posts 
        JOIN categories ON posts.category_id = categories.id 
        ORDER BY posts.id ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Bài Viết - TechNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper">
        <!-- Thanh menu quan tri -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin.php">TechNews Admin</a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-light small">Xin chào, <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong></span>
                    <a href="index.php" class="btn btn-outline-light btn-sm" target="_blank">Xem trang chủ</a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
                </div>
            </div>
        </nav>

        <div class="container my-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Danh sách bài viết</h4>
                <a href="add_post.php" class="btn btn-success btn-sm">+ Thêm bài viết mới</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;" class="text-center">ID</th>
                                    <th style="width: 90px;" class="text-center">Hình ảnh</th>
                                    <th>Tiêu đề bài viết</th>
                                    <th style="width: 180px;">Chuyên mục</th>
                                    <th style="width: 120px;">Ngày đăng</th>
                                    <th style="width: 140px;" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?= $row['id'] ?></td>
                                            <td class="text-center">
                                                <img src="uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" 
                                                     alt="Thumb" class="rounded" style="width: 65px; height: 45px; object-fit: cover;"
                                                     onerror="this.src='uploads/default.jpg'">
                                            </td>
                                            <td>
                                                <a href="detail.php?id=<?= $row['id'] ?>" target="_blank" class="text-decoration-none fw-semibold text-dark">
                                                    <?= htmlspecialchars($row['title']) ?>
                                                </a>
                                            </td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['cat_name']) ?></span></td>
                                            <td class="small text-muted"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                            <td class="text-center">
                                                <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm py-0 px-2">Sửa</a>
                                                <a href="delete_post.php?id=<?= $row['id'] ?>" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');" 
                                                   class="btn btn-danger btn-sm py-0 px-2">Xóa</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Chưa có bài viết nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <small>Hệ thống quản lý tin tức công nghệ &copy; 2026</small>
        </div>
    </footer>

</body>
</html>