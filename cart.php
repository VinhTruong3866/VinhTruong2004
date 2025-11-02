<?php
session_start();
$conn = new mysqli("localhost", "root", "", "petshop");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem giỏ hàng!'); window.location.href='login.php';</script>";
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Xóa sản phẩm khỏi giỏ hàng
if (isset($_POST['remove'])) {
    $product_id = (int)$_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

// Cập nhật số lượng sản phẩm
if (isset($_POST['update'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }
}

// Lấy danh sách sản phẩm trong giỏ hàng
$sql = "SELECT cart.product_id, products.title, products.price, products.image, cart.quantity 
        FROM cart 
        JOIN products ON cart.product_id = products.id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Petshop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f8f4fa;
            font-family: 'Segoe UI', sans-serif;
        }

        main {
            width: 90%;
            max-width: 1100px;
            margin: 50px auto;
            background: #fff;
            padding: 25px 40px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #6a0dad;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f1e5fc;
            color: #5a007a;
            font-weight: bold;
        }

        tr:hover {
            background-color: #faf5ff;
        }

        img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
        }

        input[type="number"] {
            width: 60px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-align: center;
        }

        button {
            border: none;
            border-radius: 25px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 14px;
        }

        button[name="update"] {
            background-color: #7a1fa2;
            color: white;
        }

        button[name="remove"] {
            background-color: #f44336;
            color: white;
        }

        button:hover {
            opacity: 0.85;
        }

        .checkout {
            text-align: right;
            margin-top: 20px;
        }

        .checkout h3 {
            font-size: 20px;
            color: #4a006c;
        }

        .checkout-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 25px;
            background-color: #8e24aa;
            color: white;
            border-radius: 30px;
            text-decoration: none;
            transition: 0.3s;
        }

        .checkout-btn:hover {
            background-color: #b56fe8;
        }

        .empty-cart {
            text-align: center;
            color: #777;
            font-size: 18px;
            margin-top: 50px;
        }

        .empty-cart a {
            color: #7a1fa2;
            text-decoration: none;
            font-weight: bold;
        }

        .empty-cart a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include "header.php"; ?>

<main>
    <h2>🛒 Giỏ hàng của bạn</h2>

    <?php if ($result->num_rows > 0) { ?>
        <table>
            <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Tổng</th>
                <th>Hành động</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { 
                $subtotal = $row['price'] * $row['quantity'];
                $total_price += $subtotal;
            ?>
                <tr>
                    <td><img src="uploads/<?php echo $row['image']; ?>" alt=""></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <form method="POST" class="update-form">
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1">
                            <button type="submit" name="update"><i class="fa-solid fa-rotate"></i> Cập nhật</button>
                        </form>
                    </td>
                    <td><strong><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</strong></td>
                    <td>
                        <form method="POST" class="remove-form">
                            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                            <button type="submit" name="remove"><i class="fa-solid fa-trash"></i> Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="checkout">
            <h3>Tổng cộng: <span style="color:#7a1fa2;"><?php echo number_format($total_price, 0, ',', '.'); ?>đ</span></h3>
            <a href="checkout.php" class="checkout-btn"><i class="fa-solid fa-credit-card"></i> Thanh toán</a>
        </div>

    <?php } else { ?>
        <div class="empty-cart">
            <p><i class="fa-solid fa-cart-arrow-down fa-2x" style="color:#b56fe8;"></i></p>
            <p>Giỏ hàng của bạn đang trống.</p>
            <a href="product.php">🛍️ Tiếp tục mua sắm</a>
        </div>
    <?php } ?>
</main>

<?php include "footer.php"; ?>
</body>
</html>
