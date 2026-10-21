<?php
session_start();
require_once 'db.php';

// Chi Admin moi co quyen tao user
if (!isset($_SESSION['admin']) || $_SESSION['role'] !== 'admin') {
    die("Từ chối truy cập!");
}

$error = '';
$success = '';

$fullname = '';
$username = '';
$role = 'editor';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = in_array($_POST['role'], ['admin', 'editor']) ? $_POST['role'] : 'editor';

    // Kiem tra de trong
    if (empty($fullname) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng nhập đầy đủ tất cả các trường thông tin!';
    
    // Logic Username: 4-20 ky tu, chi chu cai, so, gach duoi, khong dau cach, khong ky tu dac biet
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
        $error = 'Tên đăng nhập không hợp lệ! Chỉ được từ 4-20 ký tự, gồm chữ cái không dấu, chữ số và dấu gạch dưới (không được chứa khoảng trắng hay ký tự đặc biệt).';

    // Logic Mat khau manh: Toi thieu 8 ky tu, gom it nhat 1 chu HOA, 1 chu thuong, 1 so, 1 ky tu dac biet
    } elseif (
        strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[@#$%^&*!_\-]/', $password)
    ) {
        $error = 'Mật khẩu chưa đủ độ mạnh! Phải có ít nhất 8 ký tự, bao gồm: chữ HOA, chữ thường, chữ số và ít nhất 1 ký tự đặc biệt (@, #, $, %, ^, &, *, !, _, -).';

    // Kiem tra khop mat khau
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không trùng khớp!';

    } else {
        // Kiem tra trung lap Username trong CSDL
        $stmt_check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $res_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($res_check) > 0) {
            $error = "Tên đăng nhập '$username' đã tồn tại trong hệ thống. Vui lòng chọn tên khác!";
        } else {
            // Ma hoa mat khau va luu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $hashed_password, $fullname, $role);

            if (mysqli_stmt_execute($stmt_insert)) {
                header("Location: manage_users.php");
                exit();
            } else {
                $error = 'Lỗi hệ thống khi tạo tài khoản: ' . mysqli_error($conn);
            }
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

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <form action="add_user.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Họ và tên (*)</label>
                                    <input type="text" name="fullname" class="form-control" 
                                           placeholder="Ví dụ: Nguyễn Văn Bình" value="<?= htmlspecialchars($fullname) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tên đăng nhập (* 4-20 ký tự, không dấu, không khoảng trắng, không ký tự đặc biệt)</label>
                                    <input type="text" name="username" class="form-control" 
                                           placeholder="Ví dụ: vanbinh_2026" value="<?= htmlspecialchars($username) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Vai trò phân quyền (*)</label>
                                    <select name="role" class="form-select" required>
                                        <option value="editor" <?= ($role === 'editor') ? 'selected' : '' ?>>Biên tập viên (Chỉ thêm và sửa bài)</option>
                                        <option value="admin" <?= ($role === 'admin') ? 'selected' : '' ?>>Quản trị viên (Toàn quyền hệ thống)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Mật khẩu (* Tối thiểu 8 ký tự: có chữ HOA, chữ thường, số và ký tự đặc biệt @#$%!)</label>
                                    <input type="password" name="password" class="form-control" placeholder="Ví dụ: Binh@2026" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Xác nhận lại mật khẩu (*)</label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu..." required>
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