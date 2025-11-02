<?php
session_start();
include_once("database.php");

// Kiểm tra nếu người dùng đã đăng nhập và có quyền admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 1;

// Xử lý thêm sản phẩm nếu admin gửi form
if ($isAdmin && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    // form dùng name="name", giữ nguyên để không phải đổi form
    $title = trim($_POST['name'] ?? '');   // lưu vào cột title trong DB
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image'] ?? null;

    $errors = [];

    // Kiểm tra dữ liệu
    if ($title === '' || $price === '' || empty($image['name'])) {
        $errors[] = "Vui lòng điền đầy đủ thông tin sản phẩm!";
    } elseif (!is_numeric($price)) {
        $errors[] = "Giá sản phẩm phải là số!";
    } else {
        // Xử lý upload ảnh
        $target_dir = "images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $image_name = basename($image["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra định dạng ảnh
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Chỉ chấp nhận ảnh JPG, JPEG, PNG, hoặc WEBP!";
        } else {
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                // Thêm sản phẩm vào database
                // NOTE: dùng cột `title` vì cấu trúc bảng của bạn có `title`
                $stmt = $conn->prepare("INSERT INTO products (title, price, description, image) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sdss", $title, $price, $description, $image_name);
                    if ($stmt->execute()) {
                        $success = "Thêm sản phẩm thành công!";
                    } else {
                        $errors[] = "Lỗi khi thêm sản phẩm: " . $stmt->error;
                    }
                } else {
                    $errors[] = "Lỗi prepare statement: " . $conn->error;
                }
            } else {
                $errors[] = "Upload ảnh thất bại!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petshop - Quản trị</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <style>
        /* Một ít style để hiển thị đẹp khi test */
        .product-list { display:flex; gap:20px; flex-wrap:wrap; justify-content:flex-start; }
        .product { width:230px; background:#fff; padding:14px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,.08); text-align:center; }
        .product img{ width:100%; height:150px; object-fit:cover; border-radius:4px; }
    </style>
</head>
<body>

<?php include_once("header.php"); ?>
<main>
   <section class="products">
    <h2>Sản phẩm nổi bật</h2>
    <div class="product-list">
        <?php
        // Lấy 4 sản phẩm (nếu bạn có cột featured/noibat thì có thể đổi WHERE featured=1)
        $query = "SELECT * FROM products LIMIT 4";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            // các tên cột có thể có trong DB
            $nameCols = ['title','name','TenSP','ten','product_name'];
            $imgCols = ['image','thumbnail','thumb','img','HinhAnh'];

            while ($row = $result->fetch_assoc()) {
                // lấy tên: ưu tiên các cột khả dụng
                $rawName = '';
                foreach ($nameCols as $col) {
                    if (isset($row[$col]) && trim($row[$col]) !== '') {
                        $rawName = $row[$col];
                        break;
                    }
                }
                $name = $rawName !== '' ? htmlspecialchars($rawName) : 'Không có tên';

                // lấy ảnh: ưu tiên các cột khả dụng
                $rawImg = '';
                foreach ($imgCols as $col) {
                    if (!empty($row[$col])) {
                        $rawImg = $row[$col];
                        break;
                    }
                }

                // chuẩn hoá đường dẫn ảnh và kiểm tra tồn tại file
                $imagePath = 'images/default.jpg'; // mặc định
                if ($rawImg) {
                    // nếu là URL tuyệt đối hoặc bắt đầu bằng / thì dùng nguyên
                    if (preg_match('~^https?://~i', $rawImg) || strpos($rawImg, '/') === 0) {
                        $imagePath = $rawImg;
                    } else {
                        // thử các thư mục hay dùng
                        $candidates = [
                            "images/{$rawImg}",
                            "uploads/{$rawImg}",
                            $rawImg
                        ];
                        $found = false;
                        foreach ($candidates as $c) {
                            if (file_exists(__DIR__ . '/' . $c)) {
                                $imagePath = $c;
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            // nếu không tìm thấy, vẫn hiển thị images/rawImg (có thể do đường dẫn tương đối trên server)
                            $imagePath = "images/{$rawImg}";
                        }
                    }
                }

                // giá
                $rawPrice = 0;
                if (isset($row['price']) && is_numeric($row['price'])) {
                    $rawPrice = $row['price'];
                } elseif (isset($row['gia']) && is_numeric($row['gia'])) {
                    $rawPrice = $row['gia'];
                }

                $price = number_format($rawPrice, 0, ',', '.');
                ?>
                <div class="product">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo $name; ?>"
                         onerror="this.onerror=null; this.src='images/default.jpg'">
                    <h3><?php echo $name; ?></h3>
                    <p>Giá: <?php echo $price; ?> đ</p>
                    <a href="product_detail.php?id=<?php echo urlencode($row['id']); ?>" class="btn">Xem chi tiết</a>
                    <button class="add-to-cart" data-id="<?php echo htmlspecialchars($row['id']); ?>">Thêm vào giỏ</button>
                </div>
            <?php
            }
        } else {
            echo "<p>Không có sản phẩm nào.</p>";
        }
        ?>
    </div>
</section>

<?php if ($isAdmin) { ?>
<section class="admin-stats">
    <h2>Thống kê quản trị</h2>
    <div class="stats-container">
        <?php
        // Truy vấn và xử lý an toàn
        $usersResult = $conn->query("SELECT COUNT(*) AS total_users FROM users WHERE role_id = 2");
        $usersCount = 0;
        if ($usersResult && $row = $usersResult->fetch_assoc()) {
            $usersCount = $row['total_users'];
        }

        $contactsResult = $conn->query("SELECT COUNT(*) AS total_contacts FROM contacts");
        $contactsCount = 0;
        if ($contactsResult && $row = $contactsResult->fetch_assoc()) {
            $contactsCount = $row['total_contacts'];
        }

        $ordersResult = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
        $ordersCount = 0;
        if ($ordersResult && $row = $ordersResult->fetch_assoc()) {
            $ordersCount = $row['total_orders'];
        }
        ?>
        <div class="stat-box"><h3>Khách hàng</h3><p><?php echo $usersCount; ?></p></div>
        <div class="stat-box"><h3>Liên hệ</h3><p><?php echo $contactsCount; ?></p></div>
        <div class="stat-box"><h3>Đơn hàng</h3><p><?php echo $ordersCount; ?></p></div>
    </div>
</section>
<?php } ?>  
</body>
<!-- FOOTER -->
 <?php include("footer.php"); ?>
</html>
