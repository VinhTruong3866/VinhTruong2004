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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 🔹 Nút quay về */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }

        /* 🔹 Style nút Sửa/Xóa */
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
    </style>
</head>
<body>

<div class="admin-container">
    <h1>Quản lý Người dùng</h1>

    <!-- 🔹 Nút Back -->
    <a href="admin_dashboard.php" class="btn-back">← Quay về Trang Quản Trị</a>

    <a href="add_users.php" class="btn">Thêm Người Dùng</a> <!-- Nút thêm người dùng -->

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM users";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $role = ($row['role_id'] == 1) ? 'Admin' : 'Khách hàng';
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>{$role}</td>
                            <td>
                                <a href='edit_user.php?id={$row['id']}' class='edit-btn'>Sửa</a>
                                <a href='delete_user.php?id={$row['id']}' class='delete-btn' onclick='return confirm(\"Bạn có chắc muốn xóa người dùng này không?\")'>Xóa</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Không có người dùng nào.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
