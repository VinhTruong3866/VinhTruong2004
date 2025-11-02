<?php
session_start();
include_once("database.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$total_price = 0;
$cart_items = [];

// Lấy sản phẩm trong giỏ hàng
$sql = "SELECT c.product_id, c.quantity, p.title, p.price, p.image
        FROM cart AS c
        JOIN products AS p ON c.product_id = p.id
        WHERE c.user_id = ?";
$cartStmt = $conn->prepare($sql);
$cartStmt->bind_param("i", $user_id);
$cartStmt->execute();
$result = $cartStmt->get_result();

while ($item = $result->fetch_assoc()) {
    $subtotal = $item['quantity'] * $item['price'];
    $total_price += $subtotal;
    $cart_items[] = $item;
}

// Xử lý thanh toán
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];

    if ($total_price > 0 && !empty($address)) {
        // Tạo đơn hàng
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, payment_method, address, status)
                                VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("idss", $user_id, $total_price, $payment_method, $address);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Lưu chi tiết đơn hàng
        $stmt_detail = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price)
                                       VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt_detail->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_detail->execute();
        }

        // Xóa giỏ hàng sau khi đặt hàng
        $stmt_del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt_del->bind_param("i", $user_id);
        $stmt_del->execute();

        // Chuyển hướng đến trang thành công
        header("Location: success.php?order_id=$order_id&payment=$payment_method&amount=$total_price");
        exit();
    } else {
        echo "<script>alert('Vui lòng nhập địa chỉ và kiểm tra giỏ hàng!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - Petshop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #fdf0ff;
            font-family: 'Poppins', sans-serif;
        }
        .checkout-container {
            max-width: 1100px;
            margin: 50px auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            background: #fff;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 4px 20px rgba(140, 0, 255, 0.1);
        }
        .checkout-form h2, .order-summary h3 { color: #7a1fa2; }
        .checkout-form label { font-weight: 600; margin-top: 10px; display: block; }
        .checkout-form textarea {
            width: 100%; padding: 10px; border-radius: 8px;
            border: 1px solid #ccc; outline: none;
        }
        .checkout-form textarea:focus { border-color: #c93ff3; }
        .payment-options label { display: block; margin: 8px 0; }
        .btn-pay {
            background: linear-gradient(135deg, #ff77e9, #9b4dff);
            color: white; border: none; padding: 12px; border-radius: 10px;
            font-size: 16px; cursor: pointer; width: 100%;
            transition: all 0.3s ease;
        }
        .btn-pay:hover { opacity: 0.9; transform: scale(1.02); }
        .order-summary { border-left: 2px dashed #f0caff; padding-left: 25px; }
        .order-item {
            display: flex; align-items: center; gap: 12px; margin-bottom: 12px;
            border-bottom: 1px solid #f3e6ff; padding-bottom: 10px;
        }
        .order-item img {
            width: 60px; height: 60px; border-radius: 10px; object-fit: cover;
        }
        .order-item h4 { font-size: 15px; margin: 0; color: #444; }
        .order-item p { font-size: 13px; color: #777; margin: 2px 0 0; }
        .total { font-size: 18px; font-weight: bold; margin-top: 15px; color: #9b4dff; }
        #qr-section { text-align: center; display:none; margin-top: 10px; }
        #qr-section img { width: 180px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<?php include_once("header.php"); ?>

<main class="checkout-container">
    <form method="POST" class="checkout-form">
        <h2>Thông tin thanh toán</h2>

        <label for="address">Địa chỉ nhận hàng</label>
        <textarea name="address" id="address" rows="3" placeholder="Nhập địa chỉ nhận hàng của bạn" required></textarea>

        <h3>Phương thức thanh toán</h3>
        <div class="payment-options">
            <label><input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng (COD)</label>
            <label><input type="radio" name="payment_method" value="bank"> Chuyển khoản ngân hàng (QR)</label>
        </div>

        <div id="qr-section">
            <p>Quét mã QR để thanh toán:</p>
            <img src="1.png?amount=<?= $total_price ?>" alt="QR Thanh toán">
        </div>

        <button type="submit" class="btn-pay">Xác nhận thanh toán</button>
    </form>

    <div class="order-summary">
        <h3>Tóm tắt đơn hàng</h3>
        <?php if (!empty($cart_items)): ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                    <img src="uploads/<?= htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="<?= htmlspecialchars($item['title']); ?>">
                    <div>
                        <h4><?= htmlspecialchars($item['title']); ?></h4>
                        <p>SL: <?= $item['quantity']; ?> | <?= number_format($item['price'], 0, ',', '.'); ?>đ</p>
                    </div>
                </div>
            <?php endforeach; ?>
            <p class="total">Tổng tiền: <?= number_format($total_price, 0, ',', '.'); ?> VNĐ</p>
        <?php else: ?>
            <p>Giỏ hàng trống.</p>
        <?php endif; ?>
    </div>
</main>

<?php include_once("footer.php"); ?>

<script>
document.querySelectorAll('input[name="payment_method"]').forEach(input => {
    input.addEventListener('change', () => {
        const qr = document.getElementById('qr-section');
        qr.style.display = (document.querySelector('input[name="payment_method"]:checked').value === 'bank')
            ? 'block' : 'none';
    });
});
</script>

</body>
</html>
