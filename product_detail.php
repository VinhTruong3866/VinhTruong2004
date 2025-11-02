<?php
$conn = new mysqli("localhost", "root", "", "petshop");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$product = null;
$error_message = "";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $error_message = "Không tìm thấy sản phẩm!";
    }
} else {
    $error_message = "ID sản phẩm không hợp lệ!";
}

// Thêm bình luận
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_text'])) {
    $comment = trim($_POST['comment_text']);
    $username = trim($_POST['username']);
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (product_id, username, comment_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $id, $username, $comment);
        $stmt->execute();
        $stmt->close();
    }
}

// Lấy bình luận
$comments = [];
if (isset($id)) {
    $cmt_query = "SELECT * FROM comments WHERE product_id = $id ORDER BY created_at DESC";
    $cmt_result = $conn->query($cmt_query);
    if ($cmt_result && $cmt_result->num_rows > 0) {
        $comments = $cmt_result->fetch_all(MYSQLI_ASSOC);
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $product ? htmlspecialchars($product['title']) . " - Petshop" : "Chi tiết sản phẩm" ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php include "header.php"; ?>

<main>
    <div class="product-detail">
        <?php if ($product): ?>
            <img src="uploads/<?= htmlspecialchars($product['image'] ?: 'default.jpg') ?>" 
                 alt="<?= htmlspecialchars($product['title']) ?>">
            <h2><?= htmlspecialchars($product['title']) ?></h2>
            <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Không có mô tả')) ?></p>
            <p><strong>Giá:</strong> <?= number_format($product['price'], 0, ',', '.') ?>đ</p>

            <form action="cart.php?action=add&id=<?= $product['id'] ?>" method="POST">
                <label>Số lượng:</label>
                <input type="number" name="quantity" value="1" min="1">
                <button type="submit"><i class="fa fa-cart-plus"></i> Thêm vào giỏ</button>
            </form>

            <!-- Phần bình luận -->
            <div class="comment-section">
                <h3>Đánh giá & Bình luận</h3>
                <form method="POST">
                    <input type="text" name="username" placeholder="Tên của bạn" required>
                    <textarea name="comment_text" placeholder="Nhập bình luận..." rows="4" required></textarea>
                    <input type="submit" value="Gửi bình luận">
                </form>

                <?php if ($comments): ?>
                    <?php foreach ($comments as $cmt): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($cmt['username']) ?></strong><br>
                            <p><?= nl2br(htmlspecialchars($cmt['comment_text'])) ?></p>
                            <small><?= htmlspecialchars($cmt['created_at']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Chưa có bình luận nào.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p style="color:red;"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
    </div>
</main>

<?php include "footer.php"; ?>
</body>
</html>
