<?php
session_start();
include_once("database.php");
// Helper function to count rows in a table
function getCount($conn, $table, $where = '1') {
    $sql = "SELECT COUNT(*) as count FROM $table WHERE $where";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }
    return 0;
}

// Kiểm tra quyền admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 1;
if (!$isAdmin) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng</title>
    <link rel="stylesheet" href="style.css">
    <style>
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
    </style>
</head>
<body>
    <div class="admin-container">
        <h1>Quản lý Đơn hàng</h1>

        <?php
        // Thống kê quản trị
        $usersCount = getCount($conn, 'users', 'role_id = 2');
        $contactsCount = getCount($conn, 'contacts');
        $ordersCount = getCount($conn, 'orders');
        ?>
        <section class="admin-stats">
            <h2>Thống kê quản trị</h2>
            <div class="stats-container">
                <div class="stat-box"><h3>Khách hàng</h3><p><?= $usersCount ?></p></div>
                <div class="stat-box"><h3>Liên hệ</h3><p><?= $contactsCount ?></p></div>
                <div class="stat-box"><h3>Đơn hàng</h3><p><?= $ordersCount ?></p></div>
            </div>
        </section>

        <a href="admin_dashboard.php" class="btn-back">← Quay về Trang Quản Trị</a>

        <table>
            <thead>
                <tr>
                    <th>ID Đơn</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Tổng tiền</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT orders.*, users.username FROM orders 
                          JOIN users ON orders.user_id = users.id 
                          ORDER BY orders.created_at DESC";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['username']}</td>
                                <td>";

                        $order_id = intval($row['id']);
                        $details_query = "SELECT order_details.quantity, order_details.price, products.name 
                                          FROM order_details
                                          JOIN products ON order_details.product_id = products.id
                                          WHERE order_details.order_id = $order_id";
                        $details_result = mysqli_query($conn, $details_query);

                        if ($details_result) {
                            while ($detail = mysqli_fetch_assoc($details_result)) {
                                echo "{$detail['name']} (x{$detail['quantity']}) - " . number_format($detail['price'], 0, ',', '.') . " VNĐ<br>";
                            }
                        } else {
                            echo "Không có dữ liệu";
                        }

                        echo "</td>
                              <td>" . number_format($row['total_price'], 0, ',', '.') . " VNĐ</td>
                              <td>{$row['created_at']}</td>
                              <td>{$row['status']}</td>
                              <td>
                                  <form method='POST' action='update_order.php'>
                                      <input type='hidden' name='order_id' value='{$row['id']}'>
                                      <select name='status'>
                                          <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Chờ xác nhận</option>
                                          <option value='completed' " . ($row['status'] == 'completed' ? 'selected' : '') . ">Hoàn thành</option>
                                          <option value='canceled' " . ($row['status'] == 'canceled' ? 'selected' : '') . ">Hủy</option>
                                      </select>
                                      <button type='submit'>Cập nhật</button>
                                  </form>
                              </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Không có đơn hàng nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
