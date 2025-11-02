<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

include_once("database.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM products WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: manage_products.php");
        exit();
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>
