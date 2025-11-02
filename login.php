<?php
session_start();
$conn = new mysqli("localhost", "root", "", "petshop");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $query = "SELECT id, name, email, password, role_id FROM users WHERE name = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role'] = $user['role_id'];

            if ($user['role_id'] == 1) {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $errors[] = "❌ Mật khẩu không chính xác!";
        }
    } else {
        $errors[] = "❌ Tên đăng nhập hoặc email không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập | PetShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8e5ff, #fce4ec);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .auth-container {
            width: 400px;
            background: #fff;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .auth-container img {
            width: 80px;
            margin-bottom: 15px;
        }
        .auth-container h2 {
            color: #8a00b8;
            margin-bottom: 25px;
            font-weight: bold;
        }
        .auth-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        .auth-container button {
            width: 100%;
            background: linear-gradient(135deg, #a24bce, #e28de3);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        .auth-container button:hover {
            opacity: 0.85;
        }
        .auth-container p {
            margin-top: 15px;
            color: #555;
        }
        .auth-container a {
            color: #a24bce;
            font-weight: bold;
            text-decoration: none;
        }
        .auth-container .home-btn {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .auth-container .home-btn i {
            margin-right: 5px;
        }
        .error {
            color: red;
            background: #ffe0e0;
            border-radius: 10px;
            padding: 8px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <img src="assets/images/Logo.png" alt="PetShop Logo"> 
    <?php if (!empty($errors)) echo "<div class='error'>" . implode("<br>", $errors) . "</div>"; ?>
    <form method="POST">
        <input type="text" name="username_or_email" placeholder="Tên đăng nhập hoặc Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
    <a href="index.php" class="home-btn"><i class="fa fa-home"></i>Về trang chủ</a>
</div>

</body>
</html>
