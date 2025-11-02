<?php
session_start();
include_once("database.php");

$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 1;
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petshop - Thức ăn cho thú cưng</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <style>
        /* ======= PHẦN TIN TỨC MỚI NHẤT ======= */
        .news {
            background: linear-gradient(to bottom, #ffffff, #fff3f8);
            padding: 60px 80px;
            text-align: center;
            border-top: 2px solid #f6c1d7;
        }

        .news h2 {
            font-size: 28px;
            font-weight: 700;
            color: #6a006e;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .news-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            justify-content: center;
        }

        .news-list article {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .news-list article:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(220, 80, 140, 0.15);
        }

        .news-list article img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-bottom: 3px solid #f5d0e3;
        }

        .news-list article h3 {
            font-size: 18px;
            font-weight: 600;
            color: #4a004e;
            margin: 15px 20px 8px;
        }

        .news-list article p {
            font-size: 14px;
            color: #555;
            margin: 0 20px 20px;
            line-height: 1.6;
        }

        .news-list article a {
            display: inline-block;
            align-self: flex-start;
            margin: 0 20px 20px;
            background: linear-gradient(45deg, #f48fb1, #d81b60);
            color: #fff;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
        }

        .news-list article a:hover {
            background: linear-gradient(45deg, #d81b60, #a0006e);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .news {
                padding: 40px 20px;
            }
            .news-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<?php include("header.php"); ?>

<!-- SLIDER -->
<div class="swiper mySwiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="abc.webp" alt="Banner 1"></div>
        <div class="swiper-slide"><img src="abc1.webp" alt="Banner 2"></div>
        <div class="swiper-slide"><img src="abc2.webp" alt="Banner 3"></div>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
</div>

<!-- DANH MỤC NỔI BẬT -->
<section class="category">
    <h2>DANH MỤC NỔI BẬT</h2>
    <div class="category-list">
        <div class="cat-item"><img src="images/chocanh.jpeg" alt=""><p>Chó cảnh</p></div>
        <div class="cat-item"><img src="images/meocanh.jpeg" alt=""><p>Mèo cảnh</p></div>
        <div class="cat-item"><img src="images/thucan.jpg" alt=""><p>Thức ăn</p></div>
        <div class="cat-item"><img src="images/phukien.jpg" alt=""><p>Phụ kiện</p></div>
    </div>
</section>

<!-- SẢN PHẨM -->
<main>
    <section class="products">
        <h2>SẢN PHẨM NỔI BẬT</h2>
        <div class="product-list">
            <?php
            $query = "SELECT * FROM products ORDER BY RAND() LIMIT 8";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<img src='uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['title']) . "'>";
                    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p class='product-price'>" . number_format($row['price'], 0, ',', '.') . "đ</p>";
                    echo "<div class='product-actions'>";
                    echo "<a href='product_detail.php?id=" . $row['id'] . "' class='btn-view'>Xem chi tiết</a>";
                    echo "<button class='btn-cart' data-id='" . $row['id'] . "'>Thêm vào giỏ</button>";
                    echo "</div></div>";
                }
            } else {
                echo "<p>Không có sản phẩm nào.</p>";
            }
            ?>
        </div>
    </section>

    <!-- PHẦN TIN TỨC MỚI NHẤT -->
    <section class="news">
        <h2>TIN TỨC MỚI NHẤT</h2>
        <div class="news-list">
            <article>
                <img src="news1.jpg" alt="">
                <h3>Chăm sóc thú cưng đúng cách</h3>
                <p>Những bí quyết giúp thú cưng luôn khỏe mạnh và hạnh phúc.</p>
                <a href="#">Xem chi tiết</a>
            </article>
            <article>
                <img src="images/news2.jpg" alt="">
                <h3>Top thức ăn mèo tốt nhất 2025</h3>
                <p>Lựa chọn thức ăn phù hợp giúp mèo phát triển toàn diện.</p>
                <a href="#">Xem chi tiết</a>
            </article>
            <article>
                <img src="images/news3.jpg" alt="">
                <h3>Phụ kiện tiện ích cho boss</h3>
                <p>Khám phá những sản phẩm giúp chăm sóc thú cưng dễ dàng hơn.</p>
                <a href="#">Xem chi tiết</a>
            </article>
        </div>
    </section>
</main>

<!-- FOOTER -->
<?php include("footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        autoplay: { delay: 3000, disableOnInteraction: false },
        navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
        pagination: { el: ".swiper-pagination", clickable: true }
    });
</script>
</body>
</html>
