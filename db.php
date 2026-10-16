<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db_tintuc";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Ket noi that bai: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>