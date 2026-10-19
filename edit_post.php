<?php
session_start();
require_once 'db.php';

// Kiem tra phien dang nhap
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: admin.php");
    exit();
}

// Lay thong tin bai viet can sua bang Prepared Statement
$stmt = mysqli_prepare($conn, "SELECT * FROM posts WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($res);

if (!$post) {
    die("Bài viết không tồn tại!");
}

$error = '';

// Lay danh sach chuyen muc
$res_cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");

// Xu ly cap nhat bai viet
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $category_id = (int)$_POST['category_id'];
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $image_name = $post['image']; // Mac dinh giu lai anh cu

    if (empty($title) || empty($summary) || empty($content) || $category_id <= 0) {
        $error = 'Vui lòng điền đầy đủ tất cả các trường thông tin!';
    } else {
        // Neu nguoi dung co chon tai len anh moi
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            $file_info = pathinfo($_FILES['image']['name']);
            $file_ext = strtolower($file_info['extension']);

            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.]/', '', $file_info['basename']);
                $target_path = 'uploads/' . $new_filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_name = $new_filename;
                }
            } else {
                $error = 'Chỉ chấp nhận các định dạng ảnh: JPG, JPEG, PNG, WEBP!';
            }
        }

        // Cap nhat du lieu vao CSDL
        if (empty($error)) {
            $update_stmt = mysqli_prepare($conn, "UPDATE posts SET category_id = ?, title = ?, summary = ?, content = ?, image = ? WHERE id = ?");
            mysqli_stmt_bind_param($update_stmt, "issssi", $category_id, $title, $summary, $content, $image_name, $id);

            if (mysqli_stmt_execute($update_stmt)) {
                header("Location: admin.php");
                exit();
            } else {
                $error = 'Lỗi hệ thống khi cập nhật: ' . mysqli_error($conn);
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
    <title>Chỉnh Sửa Bài Viết - TechNews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="main-wrapper">
        <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin.php">TechNews Admin</a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-light small">Xin chào, <strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong></span>
                    <a href="admin.php" class="btn btn-outline-light btn-sm">&larr; Quay lại danh sách</a>
                    <a href="logout.php" class="btn btn-danger btn-sm">Đăng xuất</a>
                </div>
            </div>
        </nav>

        <!-- Form Chinh Sua -->
        <div class="container my-4">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="fw-bold m-0" style="color: var(--primary-red);">CHỈNH SỬA BÀI VIẾT #<?= $post['id'] ?></h5>
                        </div>
                        <div class="card-body p-4">

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <form action="edit_post.php?id=<?= $post['id'] ?>" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Tiêu đề bài viết (*)</label>
                                    <input type="text" name="title" class="form-control" 
                                           value="<?= htmlspecialchars($post['title']) ?>" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Chuyên mục (*)</label>
                                        <select name="category_id" class="form-select" required>
                                            <?php while ($cat = mysqli_fetch_assoc($res_cats)): ?>
                                                <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Hình ảnh đại diện</label>
                                        <input type="file" name="image" class="form-control" accept="image/*">
                                        <div class="mt-2 d-flex align-items-center gap-2">
                                            <small class="text-muted">Ảnh hiện tại:</small>
                                            <img src="uploads/<?= !empty($post['image']) ? $post['image'] : 'default.jpg' ?>" 
                                                 class="rounded border" style="width: 50px; height: 35px; object-fit: cover;" alt="Current">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Tóm tắt ngắn (*)</label>
                                    <textarea name="summary" class="form-control" rows="3" required><?= htmlspecialchars($post['summary']) ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small">Nội dung chi tiết bài viết (*)</label>
                                    <textarea name="content" class="form-control" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
                                </div>

                                <div class="border-top pt-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4">Cập nhật bài viết</button>
                                    <a href="admin.php" class="btn btn-secondary px-3">Hủy bỏ</a>
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
            Hệ thống quản trị tin tức &copy; 2026
        </div>
    </footer>

</body>
</html>