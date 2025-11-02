<?php
session_start();
include_once("database.php");

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

// Kiểm tra ID có tồn tại không
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Thực hiện truy vấn xóa
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Lỗi khi xóa người dùng!";
    }
} else {
    echo "Không tìm thấy ID người dùng!";
}
?>
