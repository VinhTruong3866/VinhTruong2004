<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "petshop";

// Kết nối MySQL
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Đặt charset UTF-8 để hỗ trợ tiếng Việt
$conn->set_charset("utf8mb4");
?>
