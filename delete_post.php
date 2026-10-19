<?php
session_start();
require_once 'db.php';

// Kiem tra phien dang nhap
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Kiem tra quyen: Chi Admin moi duoc phep xoa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("
        <div style='text-align:center; padding:50px; font-family:Arial, sans-serif;'>
            <h3 style='color:#c00;'>Từ chối truy cập (Access Denied)</h3>
            <p>Tài khoản của bạn là Biên tập viên, không có quyền xóa bài viết trong hệ thống!</p>
            <a href='admin.php' style='display:inline-block; padding:8px 16px; background:#0056b3; color:#fff; text-decoration:none; border-radius:4px;'>Quay lại trang quản trị</a>
        </div>
    ");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Lay ten file anh truoc khi xoa bai viet trong CSDL
    $stmt_img = mysqli_prepare($conn, "SELECT image FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt_img, "i", $id);
    mysqli_stmt_execute($stmt_img);
    $res_img = mysqli_stmt_get_result($stmt_img);
    $post_data = mysqli_fetch_assoc($res_img);

    if ($post_data) {
        $image_file = $post_data['image'];

        // Xoa file anh vat ly tren o dia (khong xoa anh default.jpg)
        if (!empty($image_file) && $image_file !== 'default.jpg') {
            $file_path = 'uploads/' . $image_file;
            if (file_exists($file_path)) {
                unlink($file_path); // Ham xoa file vat ly cua PHP
            }
        }

        // Xoa ban ghi trong CSDL bang Prepared Statement
        $stmt_del = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $id);
        mysqli_stmt_execute($stmt_del);
    }
}

header("Location: admin.php");
exit();
?>