<?php
session_start();
require_once 'db.php';

// Chi Admin moi co quyen tao user
if (!isset($_SESSION['admin']) || $_SESSION['role'] !== 'admin') {
    die("Từ chối truy cập!");
}

$errors = [];
$fullname = '';
$username = '';
$role = 'editor';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = in_array($_POST['role'], ['admin', 'editor']) ? $_POST['role'] : 'editor';

    // Kiem tra rieng cho o Ho va ten
    if (empty($fullname)) {
        $errors['fullname'] = 'Họ và tên không được để trống!';
    } elseif (!preg_match('/^[\p{L}\s]{2,50}$/u', $fullname)) {
        $errors['fullname'] = 'Họ và tên chỉ được chứa chữ cái và khoảng trắng (không chứa số, ký tự đặc biệt)!';
    }

    // Kiem tra rieng cho o Ten dang nhap
    if (empty($username)) {
        $errors['username'] = 'Tên đăng nhập không được để trống!';
    } elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]{2,18}[a-zA-Z0-9]$/', $username) || strpos($username, '__') !== false) {
        $errors['username'] = 'Tên đăng nhập phải từ 4-20 ký tự, bắt đầu bằng chữ cái, không chứa 2 dấu gạch dưới liên tiếp (__) và không kết thúc bằng _!';
    } else {
        // Kiem tra trung lap trong CSDL
        $stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $res_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($res_check) > 0) {
            $errors['username'] = "Tên đăng nhập '$username' đã tồn tại trong hệ thống!";
        }
    }

    // Kiem tra rieng cho o Mat khau
    if (empty($password)) {
        $errors['password'] = 'Mật khẩu không được để trống!';
    } elseif (
        strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[@#$%^&*!_\-]/', $password)
    ) {
        $errors['password'] = 'Mật khẩu phải từ 8 ký tự trở lên, gồm ít nhất 1 chữ HOA, 1 chữ thường, 1 số và 1 ký tự đặc biệt (@, #, $, %, ^, &, *, !, _, -)!';
    }

    // Kiem tra rieng cho o Xac nhan mat khau
    if (empty($confirm_password)) {
        $errors['confirm_password'] = 'Vui lòng xác nhận lại mật khẩu!';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Mật khẩu xác nhận không trùng khớp!';
    }

    // Neu khong co bat ky o nao bi loi thi luu vao CSDL
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_insert = mysqli_prepare($conn, "INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $hashed_password, $fullname, $role);

        if (mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['flash_msg'] = "Tạo tài khoản '$username' thành công!";
            header("Location: manage_users.php");
            exit();
        } else {
            $errors['system'] = 'Lỗi hệ thống khi tạo tài khoản: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tài Khoản Mới - TechNews Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin.php">TechNews Admin</a>
                <div class="d-flex align-items-center gap-3">
                    <a href="manage_users.php" class="btn btn-outline-light btn-sm">&larr; Quay lại danh sách</a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
                </div>
            </div>
        </nav>

        <div class="container my-4">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold m-0" style="color: var(--primary-red);">+ TẠO TÀI KHOẢN MỚI CHO HỆ THỐNG</h5>
                        </div>
                        <div class="card-body p-4">

                            <?php if (isset($errors['system'])): ?>
                                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($errors['system']) ?></div>
                            <?php endif; ?>

                            <form action="add_user.php" method="POST" novalidate>
                                <!-- O Ho va ten -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Họ và tên (*)</label>
                                    <input type="text" name="fullname" 
                                           class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>" 
                                           placeholder="Ví dụ: Nguyễn Văn Bình" 
                                           value="<?= htmlspecialchars($fullname) ?>">
                                    <?php if (isset($errors['fullname'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['fullname']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- O Ten dang nhap -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên đăng nhập (*)</label>
                                    <input type="text" name="username" 
                                           class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                                           placeholder="Ví dụ: vanbinh_2026" 
                                           value="<?= htmlspecialchars($username) ?>">
                                    <?php if (isset($errors['username'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['username']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- O Vai tro -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Vai trò phân quyền (*)</label>
                                    <select name="role" class="form-select">
                                        <option value="editor" <?= ($role === 'editor') ? 'selected' : '' ?>>Biên tập viên (Chỉ thêm và sửa bài)</option>
                                        <option value="admin" <?= ($role === 'admin') ? 'selected' : '' ?>>Quản trị viên (Toàn quyền hệ thống)</option>
                                    </select>
                                </div>

                                <!-- O Mat khau -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mật khẩu (*)</label>
                                    <input type="password" name="password" 
                                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                           placeholder="Tối thiểu 8 ký tự (chữ HOA, thường, số, ký tự đặc biệt)">
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- O Xac nhan mat khau -->
                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Xác nhận lại mật khẩu (*)</label>
                                    <input type="password" name="confirm_password" 
                                           class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                           placeholder="Nhập lại mật khẩu...">
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="border-top pt-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-success px-4">Tạo tài khoản</button>
                                    <a href="manage_users.php" class="btn btn-secondary px-3">Hủy bỏ</a>
                                </div>
                            </form>

                        </div>
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