<?php
session_start();
require_once 'db.php';

// Kiem tra session dang nhap
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Su dung Prepared Statement de xoa du lieu an toan
    $stmt = mysqli_prepare($conn, "DELETE FROM posts WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

// Xoa xong quay lai trang danh sach admin
header("Location: admin.php");
exit();
?>