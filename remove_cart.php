<?php
session_start();
include_once("database.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: cart.php");
    exit();
}

$id = $_GET['id'];
$query = "DELETE FROM cart WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: cart.php");
exit();
?>
