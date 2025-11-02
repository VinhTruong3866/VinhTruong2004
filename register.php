<?php
session_start();
$conn = new mysqli("localhost", "root", "", "petshop");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin!";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Mật khẩu xác nhận không khớp!";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Tên đăng nhập hoặc email đã tồn tại!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role_id = 2;
            $insert = $conn->prepare("INSERT INTO users (name, username, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("ssssi", $name, $username, $email, $hashed_password, $role_id);
            if ($insert->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role_id;
                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Đăng ký thất bại, vui lòng thử lại!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký | PetShop</title>
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
            width: 420px;
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
            margin: 8px 0;
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
    <h2>Đăng ký tài khoản PetShop</h2>
    <?php if (!empty($errors)) echo "<div class='error'>" . implode("<br>", $errors) . "</div>"; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Họ và tên" required>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
        <button type="submit">Đăng ký</button>
    </form>
    <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
    <a href="index.php" class="home-btn"><i class="fa fa-home"></i>Về trang chủ</a>
</div>

</body>
</html>
