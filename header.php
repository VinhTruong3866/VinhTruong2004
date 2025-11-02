<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 1;
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<header class="site-header">
    <div class="top-bar">
        <div class="container">
            <div>
                <i class="fa-solid fa-phone"></i> <a href="tel:0395166567">0395166567</a>
                <i class="fa-solid fa-envelope"></i> <a href="mailto:petshop.vn@gmail.com">petshop.vn@gmail.com</a>
            </div>
            <div>
                <a href="https://web.facebook.com/profile.php?id=61578795815540"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/_hgpetshop_/?igsh=dG41dzhpaTAxem1h"><i class="fa-brands fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@hgpetshop2025"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>
    </div>

    <div class="main-header">
        <div class="container header-flex">
            <div class="logo">
                <a href="index.php">
                    <img src="images\Logo.png" alt="Petshop Logo">
                    <span class="brand-name">Petshop</span>
                </a>
            </div>
            <nav class="nav-menu">
                <ul>
                    <li><a href="index.php">TRANG CHỦ</a></li>
                    <li><a href="product.php">SẢN PHẨM</a></li>
                    <li><a href="news.php">TIN TỨC</a></li>
                    <li><a href="contact.php">LIÊN HỆ</a></li>
                </ul>
            </nav>
            <div class="header-icons">
                <a href="search.php" class="icon"><i class="fa fa-search"></i></a>
                <?php if ($isLoggedIn): ?>
                    <a href="logout.php" class="icon"><i class="fa fa-user"></i> Đăng xuất</a>
                <?php else: ?>
                    <a href="login.php" class="icon"><i class="fa fa-user"></i> Đăng nhập</a>
                <?php endif; ?>
                <a href="cart.php" class="icon">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo $cartCount; ?></span>
                </a>
            </div>
        </div>
    </div>
</header>