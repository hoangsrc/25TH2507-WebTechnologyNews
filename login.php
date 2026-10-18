<?php
session_start();
require_once 'db.php';

// Neu da dang nhap roi thi chuyen thang vao trang admin
if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu!';
    } else {
        // Dung Prepared Statement chong SQL Injection
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        // Kiem tra mat khau
        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            $_SESSION['admin'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            header("Location: admin.php");
            exit();
        } else {
            $error = 'Tài khoản hoặc mật khẩu không chính xác!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - TechNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper d-flex align-items-center justify-content-center py-5">
        <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
            <div class="text-center mb-3">
                <span class="brand-logo fs-3">TECH<span>NEWS</span></span>
                <p class="text-muted small mt-1">Đăng nhập hệ thống quản trị</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tài khoản</label>
                    <input type="text" name="username" class="form-control" placeholder="Nhập username..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2">Đăng nhập</button>
                <a href="index.php" class="btn btn-outline-secondary w-100 btn-sm">Quay về trang chủ</a>
            </form>
        </div>
    </div>

    <footer class="footer-thanhnien py-3 text-center">
        <div class="container">
            <small class="text-muted">Hệ thống quản lý tin tức công nghệ &copy; 2026</small>
        </div>
    </footer>

</body>
</html>