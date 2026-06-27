<?php
/**
 * Admin - Add New Product Page
 */

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$db = getDB();
$errors = [];

// Form submit hua?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data nikalo
    $name        = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price       = floatval($_POST['price'] ?? 0);
    $sale_price  = !empty($_POST['sale_price']) ? floatval($_POST['sale_price']) : null;
    $image_url   = trim($_POST['image_url'] ?? '');
    $stock       = (int)($_POST['stock'] ?? 0);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Slug banao product name se
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
    $slug = trim($slug, '-');
    // Unique banao
    $slugCheck = $db->prepare("SELECT COUNT(*) FROM products WHERE slug LIKE ?");
    $slugCheck->execute([$slug . '%']);
    $count = $slugCheck->fetchColumn();
    if ($count > 0) $slug .= '-' . time();
    
    // Validation
    if (empty($name))        $errors[] = 'Product name is required.';
    if ($category_id <= 0)   $errors[] = 'Please select a category.';
    if ($price <= 0)         $errors[] = 'Valid price is required.';
    if (empty($image_url))   $errors[] = 'Image URL is required.';
    
    if (empty($errors)) {
        $stmt = $db->prepare("
            INSERT INTO products (category_id, name, slug, description, price, sale_price, image_url, stock, is_featured)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$category_id, $name, $slug, $description, $price, $sale_price, $image_url, $stock, $is_featured]);
        
        $_SESSION['flash_message'] = "Product \"$name\" added successfully!";
        $_SESSION['flash_type']    = 'success';
        header('Location: products.php');
        exit;
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Admin</title>
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
                <div class="admin-page-title">Add New Product</div>
                <div class="admin-page-subtitle">Fill in the details to add a new product</div>
            </div>
            <a href="products.php" class="btn-primary" style="background:var(--gray-600);">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; padding:16px; border-radius:var(--radius-sm); margin-bottom:24px;">
                <strong><i class="fas fa-exclamation-triangle"></i> Errors:</strong>
                <ul style="margin-top:8px; padding-left:16px;">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div style="display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start;">
                
                <!-- Main Fields -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Product Information</h3>
                    </div>
                    <div style="padding:28px; display:flex; flex-direction:column; gap:20px;">
                        
                        <div class="form-group">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" class="form-control" 
                                   placeholder="Enter product name"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5" 
                                      placeholder="Product description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="form-group">
                                <label class="form-label">Price (Rs.) *</label>
                                <input type="number" name="price" class="form-control" 
                                       placeholder="0.00" step="0.01" min="0"
                                       value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sale Price (Rs.) <small style="color:var(--gray-400);">optional</small></label>
                                <input type="number" name="sale_price" class="form-control"
                                       placeholder="Leave empty if no sale" step="0.01" min="0"
                                       value="<?= htmlspecialchars($_POST['sale_price'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div class="form-group">
                                <label class="form-label">Stock Quantity *</label>
                                <input type="number" name="stock" class="form-control" 
                                       placeholder="0" min="0"
                                       value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>" required>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Sidebar Fields -->
                <div style="display:flex; flex-direction:column; gap:20px;">
                    
                    <!-- Image -->
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Product Image</h3>
                        </div>
                        <div style="padding:20px;">
                            <div class="form-group">
                                <label class="form-label">Image URL *</label>
                                <input type="url" name="image_url" class="form-control" 
                                       placeholder="https://images.unsplash.com/..."
                                       value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>"
                                       oninput="previewImage(this.value)">
                                <small style="color:var(--gray-400); font-size:0.78rem; margin-top:6px; display:block;">
                                    Use Unsplash URLs for free high quality images
                                </small>
                            </div>
                            <!-- Image preview -->
                            <div id="imgPreview" style="margin-top:12px; border-radius:var(--radius-sm); overflow:hidden; display:none;">
                                <img id="previewImg" style="width:100%; height:180px; object-fit:cover;" alt="Preview">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Settings -->
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Settings</h3>
                        </div>
                        <div style="padding:20px;">
                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer;">
                                <input type="checkbox" name="is_featured" value="1" 
                                       <?= isset($_POST['is_featured']) ? 'checked' : '' ?>
                                       style="width:18px; height:18px; accent-color:var(--primary);">
                                <div>
                                    <div style="font-weight:600; font-size:0.9rem;">Featured Product</div>
                                    <div style="font-size:0.78rem; color:var(--gray-400);">Show on homepage</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit" style="border-radius:var(--radius-sm);">
                        <i class="fas fa-plus-circle"></i> Add Product
                    </button>
                </div>
                
            </div>
        </form>
        
    </main>
</div>
<script>
// Image URL se live preview
function previewImage(url) {
    const preview = document.getElementById('imgPreview');
    const img     = document.getElementById('previewImg');
    if (url) {
        img.src = url;
        preview.style.display = 'block';
        img.onerror = () => preview.style.display = 'none';
    } else {
        preview.style.display = 'none';
    }
}
</script>
</body>
</html>
