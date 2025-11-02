<?php
session_start();
include_once("database.php");

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    header("Location: admin_orders.php");
    exit();
}
?>
