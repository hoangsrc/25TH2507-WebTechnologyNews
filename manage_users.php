<?php
session_start();
require_once 'db.php';

// Chi Admin moi co quyen truy cap trang nay
if (!isset($_SESSION['admin']) || $_SESSION['role'] !== 'admin') {
    die("
        <div style='text-align:center; padding:50px; font-family:Arial, sans-serif;'>
            <h3 style='color:#c00;'>Từ chối truy cập (Access Denied)</h3>
            <p>Khu vực này chỉ dành riêng cho Quản trị viên tối cao!</p>
            <a href='admin.php' style='display:inline-block; padding:8px 16px; background:#0056b3; color:#fff; text-decoration:none;'>Quay lại</a>
        </div>
    ");
}

$msg = '';
$err = '';

// Xu ly Mo khoa tai khoan
if (isset($_GET['unlock_id'])) {
    $unlock_id = (int)$_GET['unlock_id'];
    $stmt_unlock = mysqli_prepare($conn, "UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
    mysqli_stmt_bind_param($stmt_unlock, "i", $unlock_id);
    if (mysqli_stmt_execute($stmt_unlock)) {
        $msg = "Đã mở khóa tài khoản thành công!";
    }
}

// Xu ly Xoa tai khoan
if (isset($_GET['del_id'])) {
    $del_id = (int)$_GET['del_id'];
    
    // Logic chan tu xoa chinh minh
    $stmt_check = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $del_id);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $target_user = mysqli_fetch_assoc($res_check);

    if ($target_user && $target_user['username'] === $_SESSION['admin']) {
        $err = "Bạn không thể tự xóa tài khoản của chính mình!";
    } else {
        $stmt_del = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $del_id);
        if (mysqli_stmt_execute($stmt_del)) {
            $msg = "Đã xóa tài khoản thành công!";
        }
    }
}

// Lay danh sach toan bo tai khoan
$sql_users = "SELECT * FROM users ORDER BY id ASC";
$res_users = mysqli_query($conn, $sql_users);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tài Khoản - TechNews Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin.php">TechNews Admin</a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-light small">Quản trị viên: <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong></span>
                    <a href="admin.php" class="btn btn-outline-light btn-sm">Quản lý bài viết</a>
                    <a href="manage_users.php" class="btn btn-warning btn-sm fw-bold">Quản lý tài khoản</a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
                </div>
            </div>
        </nav>

        <div class="container my-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">Danh sách tài khoản hệ thống</h4>
                <a href="add_user.php" class="btn btn-success btn-sm">+ Tạo tài khoản mới</a>
            </div>

            <?php if (!empty($msg)): ?>
                <div class="alert alert-success py-2"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>
            <?php if (!empty($err)): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($err) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;" class="text-center">ID</th>
                                    <th>Họ và tên</th>
                                    <th>Tên đăng nhập</th>
                                    <th style="width: 140px;" class="text-center">Vai trò</th>
                                    <th style="width: 140px;" class="text-center">Trạng thái</th>
                                    <th style="width: 150px;" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($u = mysqli_fetch_assoc($res_users)): ?>
                                    <?php 
                                        $is_locked = (!empty($u['locked_until']) && strtotime($u['locked_until']) > time());
                                    ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $u['id'] ?></td>
                                        <td><?= htmlspecialchars($u['fullname']) ?></td>
                                        <td><code><?= htmlspecialchars($u['username']) ?></code></td>
                                        <td class="text-center">
                                            <?php if ($u['role'] === 'admin'): ?>
                                                <span class="badge bg-danger">Quản trị viên</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">Biên tập viên</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($is_locked): ?>
                                                <span class="badge bg-danger">Đang bị khóa</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Hoạt động</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($is_locked): ?>
                                                <a href="manage_users.php?unlock_id=<?= $u['id'] ?>" class="btn btn-warning btn-sm py-0 px-2 fw-semibold">Mở khóa</a>
                                            <?php endif; ?>

                                            <?php if ($u['username'] !== $_SESSION['admin']): ?>
                                                <a href="manage_users.php?del_id=<?= $u['id'] ?>" 
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa tài khoản này không?');" 
                                                   class="btn btn-danger btn-sm py-0 px-2">Xóa</a>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Đang đăng nhập</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer py-3 text-center border-top">
        <div class="container small text-muted">
            Hệ thống quản lý tin tức công nghệ &copy; 2026
        </div>
    </footer>

</body>
</html>