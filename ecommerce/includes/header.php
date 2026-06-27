<?php
/**
 * Header File - Har page ka upar wala hissa
 * Navigation, cart count, user status sab yahan hai
 */

require_once __DIR__ . '/../config/database.php';

// Cart items count nikalo session se
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

// Current page URL check karo active nav link ke liye
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . SITE_NAME : SITE_NAME ?></title>
    
    <!-- Google Fonts - Modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome - Icons ke liye -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Main CSS file -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    
    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
</head>
<body>

<!-- ============================================
     ANNOUNCEMENT BAR - Top mein chhota banner
     ============================================ -->
<div class="announcement-bar">
    <div class="container">
        <p>
            <i class="fas fa-truck"></i> 
            Free shipping on orders above Rs. 2000! 
            <span class="separator">|</span>
            <i class="fas fa-tag"></i> 
            Use code <strong>SAVE20</strong> for 20% off
        </p>
    </div>
</div>

<!-- ============================================
     MAIN HEADER - Logo, Search, Cart
     ============================================ -->
<header class="main-header" id="mainHeader">
    <div class="container">
        <div class="header-inner">
            
            <!-- Logo -->
            <a href="<?= SITE_URL ?>/index.php" class="logo">
                <i class="fas fa-shopping-bag logo-icon"></i>
                <span class="logo-text"><?= SITE_NAME ?></span>
            </a>
            
            <!-- Search Bar -->
            <form class="search-bar" action="<?= SITE_URL ?>/search.php" method="GET">
                <input type="text" name="q" placeholder="Search products..." 
                       value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
            
            <!-- Header Icons - Cart, User -->
            <div class="header-actions">
                
                <!-- User Account -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-dropdown">
                        <button class="icon-btn user-btn">
                            <i class="fas fa-user"></i>
                            <span class="icon-label"><?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?></span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="<?= SITE_URL ?>/profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
                            <a href="<?= SITE_URL ?>/orders.php"><i class="fas fa-box"></i> My Orders</a>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <a href="<?= SITE_URL ?>/admin/index.php"><i class="fas fa-cog"></i> Admin Panel</a>
                            <?php endif; ?>
                            <hr>
                            <a href="<?= SITE_URL ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/login.php" class="icon-btn">
                        <i class="fas fa-user"></i>
                        <span class="icon-label">Login</span>
                    </a>
                <?php endif; ?>
                
                <!-- Shopping Cart -->
                <a href="<?= SITE_URL ?>/cart.php" class="icon-btn cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="icon-label">Cart</span>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                
            </div>
        </div>
    </div>
    
    <!-- Navigation Bar -->
    <nav class="main-nav" id="mainNav">
        <div class="container">
            <ul class="nav-links">
                <li><a href="<?= SITE_URL ?>/index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Home
                </a></li>
                <li><a href="<?= SITE_URL ?>/products.php" class="<?= $currentPage == 'products.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> All Products
                </a></li>
                
                <!-- Categories dropdown -->
                <li class="has-dropdown">
                    <a href="#"><i class="fas fa-tags"></i> Categories <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown">
                        <?php
                        // Database se categories fetch karo
                        $db = getDB();
                        $cats = $db->query("SELECT * FROM categories")->fetchAll();
                        foreach ($cats as $cat):
                        ?>
                            <li><a href="<?= SITE_URL ?>/products.php?category=<?= $cat['slug'] ?>">
                                <i class="<?= $cat['icon'] ?>"></i> <?= htmlspecialchars($cat['name']) ?>
                            </a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                
                <li><a href="<?= SITE_URL ?>/products.php?sale=1">
                    <i class="fas fa-fire"></i> <span class="sale-badge-nav">Sale</span>
                </a></li>
                <li><a href="<?= SITE_URL ?>/contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i> Contact
                </a></li>
            </ul>
        </div>
    </nav>
</header>

<!-- Flash messages dikhao (success/error) -->
<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-message flash-<?= $_SESSION['flash_type'] ?? 'success' ?>">
        <div class="container">
            <i class="fas fa-<?= ($_SESSION['flash_type'] ?? 'success') == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= htmlspecialchars($_SESSION['flash_message']) ?>
            <button class="flash-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php
    // Message dikhne ke baad hata do
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
    ?>
<?php endif; ?>

<main class="main-content">
