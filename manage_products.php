<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

include_once("database.php");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Sản phẩm</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .edit-btn, .delete-btn {
            padding: 6px 14px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 14px;
            transition: 0.2s;
        }
        .edit-btn {
            background-color: orange;
        }
        .edit-btn:hover {
            background-color: darkorange;
        }
        .delete-btn {
            background-color: red;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
        /* 🔥 Nút Back */
        .btn-back {
            display: inline-block;
            padding: 8px 15px;
            margin-bottom: 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Quản lý Sản phẩm</h1>

        <!-- 🔹 Nút Back về Dashboard -->
        <a href="admin_dashboard.php" class="btn-back">← Quay về Trang Quản Trị</a>

        <a href="add_product.php" class="btn">Thêm Sản phẩm</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tên Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM products";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    $imagePath = !empty($row['image']) ? 'uploads/' . $row['image'] : 'uploads/default.jpg';
                    
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td><img src='{$imagePath}' width='50'></td>
                            <td>{$row['title']}</td>
                            <td>" . number_format($row['price'], 0, ',', '.') . " VNĐ</td>
                            <td>{$row['stock']}</td>
                            <td>
                                <div class='action-buttons'>
                                    <a href='edit_product.php?id={$row['id']}' class='edit-btn'>Sửa</a>
                                    <a href='delete_product.php?id={$row['id']}' class='delete-btn' onclick='return confirm(\"Bạn có chắc muốn xóa không?\")'>Xóa</a>
                                </div>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>  
</html>
