<?php
session_start();
require_once 'db.php';

// Kiem tra dang nhap
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$current_role = $_SESSION['role'] ?? 'editor';

// Xu ly Duyet bai viet danh rieng cho Admin
if (isset($_GET['approve_id']) && $current_role === 'admin') {
    $approve_id = (int)$_GET['approve_id'];
    if ($approve_id > 0) {
        $stmt_app = mysqli_prepare($conn, "UPDATE posts SET status = 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt_app, "i", $approve_id);
        mysqli_stmt_execute($stmt_app);
        header("Location: admin.php");
        exit();
    }
}

// Lay danh sach tat ca bai viet
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
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin.php">TechNews Admin</a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-light small">
                        Xin chào: <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong>
                        <?php if ($current_role === 'admin'): ?>
                            <span class="badge bg-danger ms-1">Quản trị viên</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark ms-1">Biên tập viên</span>
                        <?php endif; ?>
                    </span>
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
                                    <th style="width: 45px;" class="text-center">ID</th>
                                    <th style="width: 80px;" class="text-center">Hình ảnh</th>
                                    <th>Tiêu đề bài viết</th>
                                    <th style="width: 140px;">Chuyên mục</th>
                                    <th style="width: 70px;" class="text-center">Xem</th>
                                    <th style="width: 110px;" class="text-center">Trạng thái</th>
                                    <th style="width: 170px;" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td class="text-center fw-bold"><?= $row['id'] ?></td>
                                            <td class="text-center">
                                                <img src="uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" 
                                                     alt="Thumb" class="rounded" style="width: 60px; height: 40px; object-fit: cover;"
                                                     onerror="this.src='uploads/default.jpg'">
                                            </td>
                                            <td>
                                                <a href="detail.php?id=<?= $row['id'] ?>" target="_blank" class="text-decoration-none fw-semibold text-dark">
                                                    <?= htmlspecialchars($row['title']) ?>
                                                </a>
                                            </td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['cat_name']) ?></span></td>
                                            <td class="text-center small text-muted"><?= $row['views'] ?></td>
                                            <td class="text-center">
                                                <?php if ($row['status'] == 1): ?>
                                                    <span class="badge bg-success">Đã duyệt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <!-- Nut Duyet chi hien khi bai cho duyet va nguoi dung la Admin -->
                                                <?php if ($row['status'] == 0 && $current_role === 'admin'): ?>
                                                    <a href="admin.php?approve_id=<?= $row['id'] ?>" class="btn btn-success btn-sm py-0 px-2" title="Duyệt bài xuất bản">Duyệt</a>
                                                <?php endif; ?>

                                                <a href="edit_post.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm py-0 px-2">Sửa</a>
                                                
                                                <?php if ($current_role === 'admin'): ?>
                                                    <a href="delete_post.php?id=<?= $row['id'] ?>" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');" 
                                                       class="btn btn-danger btn-sm py-0 px-2">Xóa</a>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm py-0 px-2" disabled title="Chỉ Admin mới có quyền xóa">Khóa</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Chưa có bài viết nào.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer py-3 text-center border-top">
        <div class="container small text-muted">
            Hệ thống quản trị tin tức công nghệ&copy; 2026
        </div>
    </footer>

</body>
</html>