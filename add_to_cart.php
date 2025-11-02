<?php
session_start();
$conn = new mysqli("localhost", "root", "", "petshop");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập trước khi thêm vào giỏ hàng!'); window.location.href='login.php';</script>";
    exit();
}

if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $user_id = (int)$_SESSION['user_id']; // Lấy trực tiếp từ session

    // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
    $check_cart = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($check_cart);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu có rồi thì tăng số lượng
        $update_query = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    } else {
        // Nếu chưa có thì thêm mới
        $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
    }

    echo "<script>alert('Sản phẩm đã thêm vào giỏ hàng!'); window.location.href='cart.php';</script>";
}
?>
