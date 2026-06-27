<?php
/**
 * My Orders Page - User ke saare orders
 */

require_once 'config/database.php';

// Login check - agar login nahi hai toh redirect karo
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = 'Please login to view your orders.';
    $_SESSION['flash_type']    = 'error';
    header('Location: login.php');
    exit;
}

$db = getDB();

// User ke orders fetch karo
$stmt = $db->prepare("
    SELECT o.*, COUNT(oi.id) as item_count 
    FROM orders o 
    LEFT JOIN order_items oi ON oi.order_id = o.id
    WHERE o.user_id = ? 
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

$pageTitle = 'My Orders';
require_once 'includes/header.php';
?>

<div class="page-title-bar">
    <div class="container">
        <h1><i class="fas fa-box"></i> My Orders</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>My Orders</span>
        </div>
    </div>
</div>

<div style="padding:50px 0 80px;">
    <div class="container">
        
        <?php if (empty($orders)): ?>
            <div class="empty-state" data-aos="fade-up">
                <i class="fas fa-box-open"></i>
                <h3>No orders yet</h3>
                <p>You haven't placed any orders. Start shopping now!</p>
                <a href="products.php" class="btn-primary">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
            </div>
        <?php else: ?>
            <div class="admin-card" data-aos="fade-up">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">All Orders (<?= count($orders) ?>)</h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong style="color:var(--primary);"><?= htmlspecialchars($order['order_number']) ?></strong></td>
                                <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                <td><?= $order['item_count'] ?> item(s)</td>
                                <td><strong><?= CURRENCY ?> <?= number_format($order['total_amount'], 0) ?></strong></td>
                                <td style="text-transform:capitalize;"><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
