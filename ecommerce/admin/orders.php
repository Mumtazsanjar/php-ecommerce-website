<?php
/**
 * Admin Orders Management
 */

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = getDB();

// Order status update karo
if (isset($_POST['update_status'])) {
    $orderId   = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($newStatus, $validStatuses)) {
        $db->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$newStatus, $orderId]);
        $_SESSION['flash_message'] = 'Order status updated!';
        $_SESSION['flash_type']    = 'success';
    }
    header('Location: orders.php');
    exit;
}

// All orders fetch karo - customer_name directly orders table mein bhi hai (guest orders ke liye)
$orders = $db->query("
    SELECT o.*, 
           COALESCE(o.customer_name, u.name, 'Guest') as display_name,
           COALESCE(u.email, '') as customer_email,
           o.customer_phone,
           o.customer_city,
           COUNT(oi.id) as item_count
    FROM orders o 
    LEFT JOIN users u ON u.id = o.user_id
    LEFT JOIN order_items oi ON oi.order_id = o.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="admin-main">
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div style="background:#ecfdf5; color:#065f46; padding:12px 16px; border-radius:var(--radius-sm); margin-bottom:20px; font-size:0.9rem; border:1px solid #a7f3d0;">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
        
        <div class="admin-top-bar">
            <div>
                <div class="admin-page-title">Orders</div>
                <div class="admin-page-subtitle"><?= count($orders) ?> total orders</div>
            </div>
        </div>
        
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong style="color:var(--primary);"><?= htmlspecialchars($order['order_number']) ?></strong></td>
                            <td>
                                <div style="font-weight:600; font-size:0.88rem;">
                                    <?= htmlspecialchars($order['display_name'] ?? $order['customer_name'] ?? 'Guest') ?>
                                </div>
                                <?php if (!empty($order['customer_email'])): ?>
                                    <div style="font-size:0.75rem; color:var(--gray-400);"><?= htmlspecialchars($order['customer_email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:0.85rem; color:var(--gray-600);">
                                <?= htmlspecialchars($order['customer_phone'] ?? '—') ?>
                            </td>
                            <td style="font-size:0.85rem;">
                                <?= htmlspecialchars($order['customer_city'] ?? '—') ?>
                            </td>
                            <td><?= $order['item_count'] ?> items</td>
                            <td><strong><?= CURRENCY ?> <?= number_format($order['total_amount'], 0) ?></strong></td>
                            <td style="text-transform:capitalize; font-size:0.85rem;"><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td style="font-size:0.82rem; color:var(--gray-400);"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <!-- Quick status update form -->
                                <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <select name="status" class="sort-select" style="font-size:0.8rem; padding:6px 10px;">
                                        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $order['status']==$s?'selected':'' ?>>
                                                <?= ucfirst($s) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" 
                                            style="background:var(--primary); color:white; padding:7px 12px; border-radius:6px; font-size:0.8rem;">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--gray-400);">No orders yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </main>
</div>
</body>
</html>
