<?php
/**
 * Admin Products Management Page
 * Products list, edit, delete
 */

require_once '../config/database.php';

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = getDB();

// Product delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$deleteId]);
    $_SESSION['flash_message'] = 'Product deleted successfully.';
    $_SESSION['flash_type']    = 'success';
    header('Location: products.php');
    exit;
}

// Toggle featured
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    $pid = (int)$_GET['toggle_featured'];
    $db->prepare("UPDATE products SET is_featured = NOT is_featured WHERE id = ?")->execute([$pid]);
    header('Location: products.php');
    exit;
}

// Search aur filter
$search    = trim($_GET['q'] ?? '');
$catFilter = (int)($_GET['category'] ?? 0);

$where  = ["p.is_active = 1"];
$params = [];

if ($search) {
    $where[]  = "p.name LIKE ?";
    $params[] = "%$search%";
}
if ($catFilter) {
    $where[]  = "p.category_id = ?";
    $params[] = $catFilter;
}

$whereSQL = "WHERE " . implode(" AND ", $where);

$products = $db->prepare("
    SELECT p.*, c.name as cat_name 
    FROM products p 
    JOIN categories c ON c.id = p.category_id 
    $whereSQL 
    ORDER BY p.created_at DESC
");
$products->execute($params);
$products = $products->fetchAll();

$categories = $db->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="admin-main">
        
        <!-- Flash Message -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div style="background:#ecfdf5; color:#065f46; padding:12px 16px; border-radius:var(--radius-sm); margin-bottom:20px; font-size:0.9rem; display:flex; align-items:center; gap:8px; border:1px solid #a7f3d0;">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash_message']) ?>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>
        
        <div class="admin-top-bar">
            <div>
                <div class="admin-page-title">Products</div>
                <div class="admin-page-subtitle"><?= count($products) ?> products found</div>
            </div>
            <a href="add-product.php" class="btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>
        
        <!-- Search & Filter Bar -->
        <div class="admin-card" style="margin-bottom:20px;">
            <div style="padding:16px 20px;">
                <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                    <div style="flex:1; min-width:200px;" class="input-icon-wrap">
                        <i class="fas fa-search input-icon"></i>
                        <input type="text" name="q" class="form-control" placeholder="Search products..." 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <select name="category" class="sort-select" style="min-width:160px;">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $catFilter == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-primary" style="padding:12px 20px;">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <?php if ($search || $catFilter): ?>
                        <a href="products.php" style="color:var(--gray-600); font-size:0.85rem;">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Sale Price</th>
                        <th>Stock</th>
                        <th>Rating</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <!-- Product name + image -->
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <img src="<?= htmlspecialchars($p['image_url']) ?>" 
                                         style="width:50px; height:50px; object-fit:cover; border-radius:8px; flex-shrink:0;"
                                         onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=100&q=80'"
                                         alt="">
                                    <div>
                                        <div style="font-weight:600; font-size:0.9rem; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            <?= htmlspecialchars($p['name']) ?>
                                        </div>
                                        <div style="font-size:0.75rem; color:var(--gray-400);">ID: <?= $p['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($p['cat_name']) ?></td>
                            <td><?= CURRENCY ?> <?= number_format($p['price'], 0) ?></td>
                            <td>
                                <?php if ($p['sale_price']): ?>
                                    <span style="color:var(--danger); font-weight:700;">
                                        <?= CURRENCY ?> <?= number_format($p['sale_price'], 0) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--gray-400);">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="color:<?= $p['stock'] > 10 ? 'var(--success)' : ($p['stock'] > 0 ? 'var(--accent)' : 'var(--danger)') ?>; font-weight:600;">
                                    <?= $p['stock'] ?>
                                </span>
                            </td>
                            <td>
                                <span style="color:var(--accent); font-weight:600;">
                                    <i class="fas fa-star"></i> <?= $p['rating'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="?toggle_featured=<?= $p['id'] ?>" 
                                   style="color:<?= $p['is_featured'] ? 'var(--accent)' : 'var(--gray-400)' ?>; font-size:1.2rem;">
                                    <i class="fas fa-star" title="<?= $p['is_featured'] ? 'Remove from featured' : 'Add to featured' ?>"></i>
                                </a>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="edit-product.php?id=<?= $p['id'] ?>" 
                                       style="background:rgba(99,102,241,0.1); color:var(--primary); padding:6px 12px; border-radius:6px; font-size:0.82rem; font-weight:600; transition:var(--transition);">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?= $p['id'] ?>" 
                                       onclick="return confirm('Delete this product?')"
                                       style="background:rgba(239,68,68,0.1); color:var(--danger); padding:6px 12px; border-radius:6px; font-size:0.82rem; font-weight:600; transition:var(--transition);">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center; color:var(--gray-400); padding:40px;">
                                <i class="fas fa-box-open" style="font-size:2rem; margin-bottom:10px; display:block;"></i>
                                No products found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </main>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
