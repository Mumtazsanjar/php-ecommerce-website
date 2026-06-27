<?php
/**
 * Admin Dashboard
 * Sales stats, recent orders, quick overview
 */

require_once '../config/database.php';

// Admin check - sirf admin access kar sakta hai
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = getDB();

// Dashboard stats fetch karo
$stats = [
    // Total revenue
    'revenue' => $db->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'")->fetchColumn(),
    // Total orders
    'orders'  => $db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    // Total products
    'products'=> $db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn(),
    // Total customers
    'customers'=> $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn(),
];

// Recent 10 orders
$recentOrders = $db->query("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    LEFT JOIN users u ON u.id = o.user_id
    ORDER BY o.created_at DESC 
    LIMIT 10
")->fetchAll();

// Top 5 products by reviews
$topProducts = $db->query("
    SELECT p.*, c.name as cat_name 
    FROM products p 
    JOIN categories c ON c.id = p.category_id
    WHERE p.is_active = 1
    ORDER BY p.reviews_count DESC 
    LIMIT 5
")->fetchAll();

$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    
    <!-- SIDEBAR -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- MAIN CONTENT -->
    <main class="admin-main">
        
        <!-- Top Bar -->
        <div class="admin-top-bar">
            <div>
                <div class="admin-page-title">Dashboard</div>
                <div class="admin-page-subtitle">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! Here's what's happening today.</div>
            </div>
            <div style="display:flex; gap:12px; align-items:center;">
                <span style="font-size:0.85rem; color:var(--gray-600);"><?= date('l, d M Y') ?></span>
                <a href="../index.php" class="btn-primary" style="padding:10px 18px; font-size:0.85rem;">
                    <i class="fas fa-eye"></i> View Site
                </a>
            </div>
        </div>
        
        <!-- STATS CARDS -->
        <div class="admin-stats-grid">
            
            <div class="admin-stat-card" data-aos="fade-up">
                <div class="admin-stat-icon" style="background:rgba(99,102,241,0.15);">
                    <i class="fas fa-chart-line" style="color:var(--primary);"></i>
                </div>
                <div>
                    <h3><?= CURRENCY ?> <?= number_format($stats['revenue'], 0) ?></h3>
                    <p>Total Revenue</p>
                    <span class="admin-stat-trend trend-up"><i class="fas fa-arrow-up"></i> +12.5% this month</span>
                </div>
            </div>
            
            <div class="admin-stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="admin-stat-icon" style="background:rgba(236,72,153,0.15);">
                    <i class="fas fa-shopping-bag" style="color:var(--secondary);"></i>
                </div>
                <div>
                    <h3><?= number_format($stats['orders']) ?></h3>
                    <p>Total Orders</p>
                    <span class="admin-stat-trend trend-up"><i class="fas fa-arrow-up"></i> +8 new today</span>
                </div>
            </div>
            
            <div class="admin-stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="admin-stat-icon" style="background:rgba(245,158,11,0.15);">
                    <i class="fas fa-box" style="color:var(--accent);"></i>
                </div>
                <div>
                    <h3><?= number_format($stats['products']) ?></h3>
                    <p>Active Products</p>
                    <span class="admin-stat-trend trend-up"><i class="fas fa-arrow-up"></i> 2 added recently</span>
                </div>
            </div>
            
            <div class="admin-stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="admin-stat-icon" style="background:rgba(16,185,129,0.15);">
                    <i class="fas fa-users" style="color:var(--success);"></i>
                </div>
                <div>
                    <h3><?= number_format($stats['customers']) ?></h3>
                    <p>Customers</p>
                    <span class="admin-stat-trend trend-up"><i class="fas fa-arrow-up"></i> +15 this week</span>
                </div>
            </div>
            
        </div>
        
        <div style="display:grid; grid-template-columns:1fr 340px; gap:24px;">
            
            <!-- Recent Orders Table -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><i class="fas fa-shopping-bag"></i> Recent Orders</h3>
                    <a href="orders.php" class="btn-primary" style="padding:8px 16px; font-size:0.82rem;">View All</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong style="color:var(--primary);"><?= htmlspecialchars($order['order_number']) ?></strong></td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></td>
                                <td><strong><?= CURRENCY ?> <?= number_format($order['total_amount'], 0) ?></strong></td>
                                <td style="text-transform:capitalize;"><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td style="font-size:0.82rem; color:var(--gray-400);">
                                    <?= date('d M', strtotime($order['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="6" style="text-align:center; color:var(--gray-400); padding:30px;">No orders yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Top Products -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><i class="fas fa-star"></i> Top Products</h3>
                </div>
                <div style="padding:16px;">
                    <?php foreach ($topProducts as $tp): ?>
                        <div style="display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid var(--gray-100);">
                            <img src="<?= htmlspecialchars($tp['image_url']) ?>" 
                                 style="width:48px; height:48px; object-fit:cover; border-radius:8px; flex-shrink:0;"
                                 onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=100&q=80'"
                                 alt="">
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:0.85rem; font-weight:600; color:var(--dark); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    <?= htmlspecialchars($tp['name']) ?>
                                </div>
                                <div style="font-size:0.75rem; color:var(--gray-400);"><?= $tp['cat_name'] ?></div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <div style="font-weight:700; color:var(--primary); font-size:0.85rem;">
                                    <?= CURRENCY ?> <?= number_format($tp['sale_price'] ?: $tp['price'], 0) ?>
                                </div>
                                <div style="font-size:0.72rem; color:var(--accent);">
                                    <i class="fas fa-star"></i> <?= $tp['rating'] ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        </div>
        
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="../assets/js/main.js"></script>
<script>AOS.init({ duration: 600, once: true });</script>
</body>
</html>
