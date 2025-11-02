<?php
session_start();
$conn = new mysqli("localhost", "root", "", "petshop");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy từ khóa tìm kiếm
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Nếu không có từ khóa -> không cần truy vấn
$result = null;
if ($query !== '') {
    if (is_numeric($query)) {
        // Tìm theo ID hoặc theo tên/mô tả
        $sql = "SELECT * FROM products WHERE id = ? OR title LIKE ? OR description LIKE ?";
        $stmt = $conn->prepare($sql);
        $search = "%" . $query . "%";
        $stmt->bind_param("iss", $query, $search, $search);
    } else {
        // Tìm theo tên hoặc mô tả
        $sql = "SELECT * FROM products WHERE title LIKE ? OR description LIKE ?";
        $stmt = $conn->prepare($sql);
        $search = "%" . $query . "%";
        $stmt->bind_param("ss", $search, $search);
    }

    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        main {
            padding: 50px 10%;
            background: #fafafa;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .search-box {
            text-align: center;
            margin-bottom: 30px;
        }
        .search-box input {
            padding: 8px 12px;
            width: 40%;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .search-box button {
            padding: 8px 16px;
            background: #6a0dad;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .search-box button:hover {
            background: #540c91;
        }
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }
        .product {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            text-align: center;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        .product img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .product h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 8px;
        }
        .product p {
            font-size: 16px;
            color: #ff6b35;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            margin-top: 10px;
            background: #28a745;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #218838;
        }
        .no-result {
            text-align: center;
            color: #777;
            font-size: 18px;
            padding: 30px 0;
        }
    </style>
</head>
<body>

<?php include "header.php"; ?>

<main>
    <h2>Kết quả tìm kiếm cho: “<?php echo htmlspecialchars($query); ?>”</h2>

    <!-- Form tìm kiếm (đã đóng đúng) -->
    <form action="search.php" method="get" class="search-box">
        <input type="text" name="query" placeholder="Nhập tên, mô tả hoặc ID sản phẩm..." 
               value="<?php echo htmlspecialchars($query); ?>">
        <button type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
    </form>

    <?php if ($query !== ''): ?>
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="product-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product">
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo number_format($row['price'], 0, ',', '.'); ?>đ</p>
                        <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn">Xem chi tiết</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-result">Không tìm thấy sản phẩm nào.</p>
        <?php endif; ?>
    <?php endif; ?>
</main>

<!-- NỘI DUNG FOOTER -->
    <div class="footer-content">
        <div class="footer-col">
            <h3>LIÊN HỆ</h3>
            <p><i class="fa-solid fa-location-dot"></i> 316 ngõ 192 Lê Trọng Tấn Định Công Hà Nội </p>

            <p><i class="fa-solid fa-phone"></i> 0395166567</p>

            <p><i class="fa-solid fa-envelope"></i> support@petshop.vn</p>

            <div class="social-icons">
                <a href="https://web.facebook.com/profile.php?id=61578795815540"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/_hgpetshop_/?igsh=dG41dzhpaTAxem1h"><i class="fa-brands fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@hgpetshop2025"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h3>CHÍNH SÁCH</h3>
            <ul>
                <li><a href="#">Chính sách bảo mật</a></li>
                <li><a href="#">Chính sách đổi trả</a></li>
                <li><a href="#">Điều khoản dịch vụ</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>HỖ TRỢ KHÁCH HÀNG</h3>
            <ul>
                <li><a href="#">Hướng dẫn mua hàng</a></li>
                <li><a href="#">Phương thức thanh toán</a></li>
                <li><a href="#">Liên hệ hỗ trợ</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>Copyrights © 2017 by <strong>Petshop.vn</strong>  Powered by  <span>Gaugaumeomeo</span></p>
    </div>
</footer>

</body>
</html>
