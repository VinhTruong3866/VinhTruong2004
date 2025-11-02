<?php
// Kết nối MySQL
$conn = new mysqli("localhost", "root", "", "petshop");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            echo "<script>alert('✅ Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');</script>";
        } else {
            echo "<script>alert('❌ Lỗi khi gửi thông tin, vui lòng thử lại.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Liên hệ | PetShop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* --- CONTACT PAGE STYLE --- */
        body {
            background-color: #faf4f8;
            font-family: "Segoe UI", sans-serif;
        }

        .contact-container {
            width: 90%;
            max-width: 1200px;
            margin: 60px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 40px;
            align-items: start;
        }

        .contact-info {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .contact-info h2 {
            color: #5a007a;
            font-size: 26px;
            margin-bottom: 20px;
        }

        .contact-info p {
            color: #555;
            font-size: 15px;
            margin: 10px 0;
            line-height: 1.6;
        }

        .contact-info i {
            color: #b56fe8;
            margin-right: 10px;
        }

        .contact-form {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .contact-form h2 {
            color: #5a007a;
            font-size: 26px;
            margin-bottom: 20px;
            text-align: center;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
        }

        .contact-form button {
            background: #7a1fa2;
            color: #fff;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }

        .contact-form button:hover {
            background: #b56fe8;
        }

        .map-container {
            grid-column: 1 / -1;
            margin-top: 50px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }

        iframe {
            width: 100%;
            height: 400px;
            border: none;
        }

        @media(max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php include("header.php"); ?>

<div class="contact-container">
    <div class="contact-info">
        <h2>Thông tin liên hệ</h2>
        <p><i class="fa-solid fa-location-dot"></i> 316 ngõ 192 Lê Trọng Tấn Định Công Hà Nội </p>
        <p><i class="fa-solid fa-phone"></i> Hotline: <b>0395166567</b></p>
        <p><i class="fa-solid fa-envelope"></i> Email: support@petshop.vn</p>
        <p><i class="fa-solid fa-clock"></i> Giờ làm việc: 8:00 - 21:00 (Thứ 2 - CN)</p>
        <hr style="margin: 20px 0; border: 1px solid #eee;">
        <p>HGPetShop luôn sẵn sàng lắng nghe và hỗ trợ bạn trong mọi vấn đề liên quan đến thú cưng 🐶🐾.<br>
        Hãy để lại thông tin của bạn, chúng tôi sẽ phản hồi nhanh nhất có thể 💕</p>
    </div>

    <div class="contact-form">
        <h2>Liên hệ với chúng tôi</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Họ và tên" required>
            <input type="email" name="email" placeholder="Email" required>
            <textarea name="message" placeholder="Lời nhắn" rows="6" required></textarea>
            <button type="submit">Gửi liên hệ</button>
        </form>
    </div>

    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.481043326146!2d106.68218817480516!3d10.775960959258018!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3a13e4cc17%3A0xb8b81e48f3cf1898!2zMTIzIE5ndXnhu4VuIFRyw6FpLCBQaMaw4budbmcgMywgUXXhuq1uIDUsIFRow6BuaCBwaOG7kSBI4buTIENow60gTWluaCwgVmlldG5hbQ!5e0!3m2!1svi!2s!4v1697783000000!5m2!1svi!2s"
            allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

<?php include("footer.php"); ?>

</body>
</html>
