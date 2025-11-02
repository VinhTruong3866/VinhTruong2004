<?php
$conn = new mysqli("localhost", "root", "", "petshop");
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - Petshop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include_once("header.php"); ?>

<main class="products">
    <h2>Danh sách sản phẩm</h2>
    <div class="product-list">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="product">
                <img src="uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg'; ?>" 
                     alt="<?= htmlspecialchars($row['title']); ?>" class="product-img">
                <h3><?= htmlspecialchars($row['title']); ?></h3>
                <p class="product-price">Giá: <?= number_format($row['price'], 0, ',', '.'); ?>đ</p>

                <div class="product-actions">
                    <form method="POST" action="add_to_cart.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1">
                        <button type="submit" class="btn-cart"><i class="fa fa-cart-plus"></i> Thêm vào giỏ</button>
                    </form>
                    <a href="product_detail.php?id=<?= $row['id']; ?>" class="btn-view">
                        <i class="fa fa-eye"></i> Xem chi tiết
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</main>

<?php include_once("footer.php"); ?>
</body>
</html>
<?php $conn->close(); ?>
