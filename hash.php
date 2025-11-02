<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Mã hóa mật khẩu</title>
</head>
<body>
    <h2>Tạo mật khẩu đã mã hóa</h2>
    <form method="POST">
        <input type="text" name="password" placeholder="Nhập mật khẩu cần mã hóa" required>
        <button type="submit">Mã hóa</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'];
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        echo "<p><strong>Mật khẩu đã mã hóa:</strong> <br><code>$hashed</code></p>";
    }
    ?>
</body>
</html>
