<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

include_once("database.php");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Kiểm tra username hoặc email đã tồn tại chưa
    $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Tên đăng nhập hoặc Email đã tồn tại!";
    } else {
        // Mã hóa mật khẩu trước khi lưu vào database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Chèn người dùng mới vào database
        $insert_query = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssi", $username, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            header("Location: manage_users.php");
            exit();
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm người dùng!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Người Dùng</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="admin-container">
    <h1>Thêm Người Dùng</h1>

    <?php 
    if (!empty($errors)) { 
        echo "<div class='error'>" . implode("<br>", $errors) . "</div>"; 
    } 
    ?>

    <form method="POST">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" name="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" required>

        <label for="role">Vai trò:</label>
        <select name="role">
            <option value="0">Khách hàng</option>
            <option value="1">Admin</option>
        </select>

        <button type="submit" class="btn">Thêm</button>
    </form>

    <a href="admin_dashboard.php" class="back-btn">Quay lại</a>
</div>

</body>
</html>
