<?php
/**
 * Admin Sidebar - Navigation
 * Har admin page par include hota hai
 */
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    
    <!-- Admin Logo -->
    <div class="admin-logo">
        <i class="fas fa-shopping-bag"></i>
        <span><?= SITE_NAME ?> Admin</span>
    </div>
    
    <!-- Navigation Links -->
    <nav class="admin-nav">
        
        <div class="admin-nav-section">
            <div class="admin-nav-label">Main</div>
            <a href="index.php" class="admin-nav-link <?= $currentAdminPage == 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-label">Catalog</div>
            <a href="products.php" class="admin-nav-link <?= $currentAdminPage == 'products.php' ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="add-product.php" class="admin-nav-link <?= $currentAdminPage == 'add-product.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>
            <a href="categories.php" class="admin-nav-link <?= $currentAdminPage == 'categories.php' ? 'active' : '' ?>">
                <i class="fas fa-tags"></i> Categories
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-label">Sales</div>
            <a href="orders.php" class="admin-nav-link <?= $currentAdminPage == 'orders.php' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="customers.php" class="admin-nav-link <?= $currentAdminPage == 'customers.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Customers
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-label">Account</div>
            <a href="../index.php" class="admin-nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
            <a href="../logout.php" class="admin-nav-link" style="color:#ef4444;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        
    </nav>
    
    <!-- Admin user info at bottom -->
    <div style="position:absolute; bottom:0; left:0; right:0; padding:16px; border-top:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; background:linear-gradient(135deg,var(--primary),var(--secondary)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.85rem; color:white; font-weight:700; flex-shrink:0;">
            <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
        </div>
        <div style="min-width:0;">
            <div style="font-size:0.85rem; font-weight:600; color:white; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                <?= htmlspecialchars($_SESSION['user_name']) ?>
            </div>
            <div style="font-size:0.72rem; color:var(--gray-400);">Administrator</div>
        </div>
    </div>
    
</aside>
