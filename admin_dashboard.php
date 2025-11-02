<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

include_once("database.php");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Chào mừng Admin, <?php echo $_SESSION['username']; ?>!</h1>
        <nav>
            <ul>
                <li><a href="manage_users.php">Quản lý Người dùng</a></li>
                <li><a href="manage_products.php">Quản lý Sản phẩm</a></li>
                <li><a href="manage_oders.php">Quản lý Đơn hàng</a></li>
                <li><a href="index.php">Thoát</a></li>
            </ul>
        </nav>
</body>
</html>
