<?php
/**
 * Admin Customers Page
 */

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = getDB();

$customers = $db->query("
    SELECT u.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount),0) as total_spent
    FROM users u 
    LEFT JOIN orders o ON o.user_id = u.id
    WHERE u.role = 'customer'
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    <?php include 'includes/sidebar.php'; ?>
    <main class="admin-main">
        
        <div class="admin-top-bar">
            <div>
                <div class="admin-page-title">Customers</div>
                <div class="admin-page-subtitle"><?= count($customers) ?> registered customers</div>
            </div>
        </div>
        
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:38px; height:38px; background:linear-gradient(135deg,var(--primary),var(--secondary)); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:0.85rem; flex-shrink:0;">
                                        <?= strtoupper(substr($c['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:600;"><?= htmlspecialchars($c['name']) ?></div>
                                        <div style="font-size:0.78rem; color:var(--gray-400);"><?= htmlspecialchars($c['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="color:var(--gray-600);"><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                            <td>
                                <span style="background:rgba(99,102,241,0.1); color:var(--primary); padding:4px 12px; border-radius:20px; font-weight:700; font-size:0.82rem;">
                                    <?= $c['order_count'] ?> orders
                                </span>
                            </td>
                            <td><strong style="color:var(--primary);"><?= CURRENCY ?> <?= number_format($c['total_spent'], 0) ?></strong></td>
                            <td style="font-size:0.82rem; color:var(--gray-400);">
                                <?= date('d M Y', strtotime($c['created_at'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($customers)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--gray-400);">No customers yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </main>
</div>
</body>
</html>
