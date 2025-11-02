<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

include_once("database.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['name']);
    $price = (int)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image = "";

    if ($price > 0 && $stock >= 0) {
        // Tạo thư mục uploads nếu chưa có
        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Xử lý ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmp = $_FILES['image']['tmp_name'];
            $imageName = basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;

            if (move_uploaded_file($imageTmp, $imagePath)) {
                $image = $imageName;
            } else {
                $message = "<p class='error'>Lỗi khi tải ảnh lên!</p>";
            }
        }

        // Thêm sản phẩm vào database
        $query = "INSERT INTO products (title, price, stock, image) VALUES ('$title', '$price', '$stock', '$image')";
        if (mysqli_query($conn, $query)) {
            $message = "<p class='success'>Sản phẩm đã được thêm thành công!</p>";
        } else {
            $message = "<p class='error'>Lỗi: " . mysqli_error($conn) . "</p>";
        }
    } else {
        $message = "<p class='error'>Vui lòng nhập giá và số lượng hợp lệ!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản phẩm</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-container">
        <h1>Thêm Sản phẩm</h1>
        <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="name">Tên sản phẩm:</label>
            <input type="text" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>

            <label for="price">Giá (VNĐ):</label>
            <input type="number" name="price" min="1" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>" required>

            <label for="stock">Số lượng:</label>
            <input type="number" name="stock" min="0" value="<?php echo isset($_POST['stock']) ? $_POST['stock'] : ''; ?>" required>

            <label for="image">Ảnh sản phẩm:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit" class="btn">Thêm Sản phẩm</button>
            <a href="admin_dashboard.php" class="btn-back">Quay lại</a>
        </form>
    </div>
</body>
</html>
