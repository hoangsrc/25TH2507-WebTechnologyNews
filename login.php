<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

$error = '';

// Bien danh dau o nao bi loi: 'username' hoac 'password' hoac 'all'
$err_field = ''; 

$saved_username = isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : $saved_username;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    if (empty($username) && empty($password)) {
        $error = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu!';
        $err_field = 'all';
    } elseif (empty($username)) {
        $error = 'Tài khoản không được để trống!';
        $err_field = 'username';
    } elseif (empty($password)) {
        $error = 'Mật khẩu không được để trống!';
        $err_field = 'password';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $is_locked = (!empty($user['locked_until']) && strtotime($user['locked_until']) > time());

            if ($is_locked) {
                $time_left = ceil((strtotime($user['locked_until']) - time()) / 60);
                $error = "Tài khoản '$username' đang bị tạm khóa (còn khoảng $time_left phút). Vui lòng liên hệ Admin để mở khóa!";
                $err_field = 'username';
            } else {
                if (password_verify($password, $user['password']) || $password === $user['password']) {
                    // Reset so lan sai
                    $stmt_reset = mysqli_prepare($conn, "UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                    mysqli_stmt_bind_param($stmt_reset, "i", $user['id']);
                    mysqli_stmt_execute($stmt_reset);

                    if ($remember) {
                        setcookie('remember_user', $username, time() + (86400 * 7), "/");
                    } else {
                        setcookie('remember_user', '', time() - 3600, "/");
                    }

                    $_SESSION['admin'] = $user['username'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['role'] = $user['role'];

                    header("Location: admin.php");
                    exit();
                } else {
                    $attempts = $user['failed_attempts'] + 1;
                    if ($attempts >= 5) {
                        $stmt_lock = mysqli_prepare($conn, "UPDATE users SET failed_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?");
                        mysqli_stmt_bind_param($stmt_lock, "ii", $attempts, $user['id']);
                        mysqli_stmt_execute($stmt_lock);
                        $error = "Tài khoản '$username' đã nhập sai 5 lần và bị khóa 15 phút. Liên hệ Admin để mở khóa!";
                    } else {
                        $stmt_fail = mysqli_prepare($conn, "UPDATE users SET failed_attempts = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmt_fail, "ii", $attempts, $user['id']);
                        mysqli_stmt_execute($stmt_fail);
                        $remaining = 5 - $attempts;
                        $error = "Mật khẩu không chính xác! (Còn lại $remaining lần thử)";
                    }
                    
                    // Vieng do o mat khau
                    $err_field = 'password';
                }
            }
        } else {
            $error = 'Tài khoản không tồn tại trên hệ thống!';

             // Vieng do o tai khoan
            $err_field = 'username';
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
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper d-flex align-items-center justify-content-center py-5">
        <div class="card shadow-sm p-4" style="max-width: 420px; width: 100%;">
            <div class="text-center mb-3">
                <span class="site-logo fs-3">TECHNEWS<span>.vn</span></span>
                <p class="text-muted small mt-1">Hệ thống đăng nhập quản trị</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tài khoản</label>
                    <input type="text" name="username" 
                           class="form-control <?= ($err_field === 'username' || $err_field === 'all') ? 'is-invalid' : '' ?>" 
                           placeholder="Nhập username..." value="<?= htmlspecialchars($username) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Mật khẩu</label>
                    <input type="password" name="password" 
                           class="form-control <?= ($err_field === 'password' || $err_field === 'all') ? 'is-invalid' : '' ?>" 
                           placeholder="Nhập mật khẩu...">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe" 
                           <?= !empty($saved_username) ? 'checked' : '' ?>>
                    <label class="form-check-label small text-muted" for="rememberMe">Ghi nhớ tài khoản trên thiết bị này</label>
                </div>

                <button type="submit" class="btn btn-danger w-100 mb-2 py-2 fw-semibold">Đăng nhập</button>
                <a href="index.php" class="btn btn-outline-secondary w-100 btn-sm">Quay về trang chủ</a>
            </form>
        </div>
    </div>

    <footer class="site-footer py-3 text-center border-top">
        <div class="container small text-muted">
            Hệ thống quản lý tin tức công nghệ &copy; 2026
        </div>
    </footer>

</body>
</html>