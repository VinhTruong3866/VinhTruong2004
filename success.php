<?php
session_start();
include_once("database.php");

// Lấy dữ liệu từ URL
$order_id = $_GET['order_id'] ?? 0;
$payment = $_GET['payment'] ?? 'cod';
$amount = $_GET['amount'] ?? 0;

// Lấy danh sách sản phẩm trong đơn hàng
$sql = "SELECT od.product_id, od.quantity, od.price, p.title, p.image 
        FROM order_details AS od
        JOIN products AS p ON od.product_id = p.id
        WHERE od.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công - Petshop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffe0ff, #e9d0ff);
            margin: 0;
            padding: 0;
        }

        .success-container {
            max-width: 900px;
            margin: 60px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(150, 50, 200, 0.15);
            text-align: center;
            padding: 40px 60px;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(30px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .success-icon {
            font-size: 90px;
            color: #b14dff;
            margin-bottom: 15px;
            animation: pop 0.6s ease-in-out;
        }

        @keyframes pop {
            0% {transform: scale(0.5);}
            100% {transform: scale(1);}
        }

        .success-container h1 {
            color: #7b2cbf;
            font-size: 26px;
            margin-bottom: 10px;
        }

        .success-container p {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .order-info {
            background: #f9f0ff;
            border-radius: 12px;
            padding: 20px;
            margin: 20px auto;
            text-align: left;
        }

        .order-info p {
            color: #333;
            font-weight: 500;
            margin: 6px 0;
        }

        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 25px;
        }

        .product {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            padding: 10px;
            text-align: center;
            transition: 0.3s;
        }

        .product:hover {
            transform: translateY(-4px);
            box-shadow: 0 5px 12px rgba(150, 50, 200, 0.2);
        }

        .product img {
            width: 100%;
            height: 130px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .product h4 {
            color: #7a1fa2;
            font-size: 15px;
            margin: 5px 0;
        }

        .product p {
            font-size: 13px;
            color: #666;
            margin: 3px 0;
        }

        .back-btn {
            display: inline-block;
            background: linear-gradient(135deg, #ff77e9, #9b4dff);
            color: #fff;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            margin-top: 25px;
        }

        .back-btn:hover {
            opacity: 0.9;
            transform: scale(1.03);
        }
    </style>
</head>
<body>

    <div class="success-container">
        <div class="success-icon">💜</div>
        <h1>Đặt hàng thành công!</h1>
        <p>Cảm ơn bạn đã mua hàng tại <strong>Petshop</strong> 🐾<br>Chúng tôi sẽ sớm liên hệ để xác nhận đơn hàng của bạn.</p>

        <div class="order-info">
            <p><strong>Mã đơn hàng:</strong> #<?= htmlspecialchars($order_id) ?></p>
            <p><strong>Tổng tiền:</strong> <?= number_format($amount, 0, ',', '.') ?>đ</p>
            <p><strong>Phương thức thanh toán:</strong>
                <?= $payment == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng' ?>
            </p>
        </div>

        <?php if (!empty($items)): ?>
        <h3>Sản phẩm bạn đã đặt:</h3>
        <div class="product-list">
            <?php foreach ($items as $item): ?>
                <div class="product">
                    <img src="uploads/<?= htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="<?= htmlspecialchars($item['title']); ?>">
                    <h4><?= htmlspecialchars($item['title']); ?></h4>
                    <p>SL: <?= $item['quantity']; ?></p>
                    <p>Giá: <?= number_format($item['price'], 0, ',', '.'); ?>đ</p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <a href="index.php" class="back-btn">← Quay lại Trang chủ</a>
    </div>

</body>
</html>
                