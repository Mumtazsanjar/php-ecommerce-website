<?php
/**
 * Products Listing Page
 * Category filter, search, sorting ke saath
 */

require_once 'config/database.php';
$db = getDB();

// URL parameters se filters nikalo
$categorySlug = $_GET['category'] ?? '';
$search       = trim($_GET['q'] ?? '');
$sortBy       = $_GET['sort'] ?? 'newest';
$onSale       = isset($_GET['sale']) && $_GET['sale'] == 1;
$page         = max(1, (int)($_GET['page'] ?? 1));
$perPage      = 12; // Har page par kitne products

// Category info fetch karo agar filter hai
$currentCategory = null;
if ($categorySlug) {
    $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$categorySlug]);
    $currentCategory = $stmt->fetch();
}

// SQL query conditions banao
$conditions = ['p.is_active = 1'];
$params = [];

if ($currentCategory) {
    $conditions[] = 'p.category_id = ?';
    $params[] = $currentCategory['id'];
}

if ($search) {
    $conditions[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($onSale) {
    $conditions[] = 'p.sale_price IS NOT NULL';
}

$whereSQL = 'WHERE ' . implode(' AND ', $conditions);

// Sorting
$orderSQL = match($sortBy) {
    'price_asc'  => 'ORDER BY COALESCE(p.sale_price, p.price) ASC',
    'price_desc' => 'ORDER BY COALESCE(p.sale_price, p.price) DESC',
    'rating'     => 'ORDER BY p.rating DESC',
    'popular'    => 'ORDER BY p.reviews_count DESC',
    default      => 'ORDER BY p.created_at DESC'
};

// Total count nikalo pagination ke liye
$countStmt = $db->prepare("SELECT COUNT(*) FROM products p $whereSQL");
$countStmt->execute($params);
$totalProducts = $countStmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// Products fetch karo
$offset = ($page - 1) * $perPage;
$productsStmt = $db->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    $whereSQL $orderSQL 
    LIMIT $perPage OFFSET $offset
");
$productsStmt->execute($params);
$products = $productsStmt->fetchAll();

// Saari categories sidebar ke liye
$allCategories = $db->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
    GROUP BY c.id
")->fetchAll();

$pageTitle = $currentCategory ? $currentCategory['name'] : ($search ? "Search: $search" : 'All Products');

require_once 'includes/header.php';
?>

<!-- Page Title Bar -->
<div class="page-title-bar">
    <div class="container">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Products</span>
            <?php if ($currentCategory): ?>
                <i class="fas fa-chevron-right"></i>
                <span><?= htmlspecialchars($currentCategory['name']) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px; padding-bottom:80px;">
    <div style="display:grid; grid-template-columns:260px 1fr; gap:32px; align-items:start;">
        
        <!-- ============================================
             SIDEBAR - Filters
             ============================================ -->
        <aside class="sidebar">
            
            <!-- Categories Filter -->
            <div class="sidebar-card">
                <h3 class="sidebar-title"><i class="fas fa-th-large"></i> Categories</h3>
                <ul class="sidebar-list">
                    <li>
                        <a href="products.php" class="<?= !$categorySlug ? 'active' : '' ?>">
                            <span>All Products</span>
                            <span class="count"><?= $totalProducts ?></span>
                        </a>
                    </li>
                    <?php foreach ($allCategories as $cat): ?>
                        <li>
                            <a href="products.php?category=<?= $cat['slug'] ?>" 
                               class="<?= $categorySlug == $cat['slug'] ? 'active' : '' ?>">
                                <span><i class="<?= $cat['icon'] ?>" style="width:18px; color:var(--primary)"></i> <?= htmlspecialchars($cat['name']) ?></span>
                                <span class="count"><?= $cat['product_count'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Price Filter -->
            <div class="sidebar-card" style="margin-top:20px;">
                <h3 class="sidebar-title"><i class="fas fa-tag"></i> Filter</h3>
                <a href="products.php?<?= $categorySlug ? 'category='.$categorySlug.'&' : '' ?>sale=1" 
                   class="sidebar-filter-btn <?= $onSale ? 'active' : '' ?>">
                    <i class="fas fa-fire"></i> On Sale Only
                </a>
            </div>
            
        </aside>

        <!-- ============================================
             PRODUCTS AREA
             ============================================ -->
        <div class="products-area">
            
            <!-- Toolbar - Sort aur count -->
            <div class="products-toolbar">
                <p class="results-count">
                    Showing <strong><?= count($products) ?></strong> of <strong><?= $totalProducts ?></strong> products
                    <?= $search ? " for \"<em>$search</em>\"" : '' ?>
                </p>
                <form class="sort-form" method="GET">
                    <?php if ($categorySlug): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
                    <?php endif; ?>
                    <?php if ($search): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">
                    <?php endif; ?>
                    <label for="sort" style="font-size:0.85rem;color:var(--gray-600);">Sort by:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" class="sort-select">
                        <option value="newest" <?= $sortBy=='newest'?'selected':'' ?>>Newest First</option>
                        <option value="popular" <?= $sortBy=='popular'?'selected':'' ?>>Most Popular</option>
                        <option value="rating" <?= $sortBy=='rating'?'selected':'' ?>>Top Rated</option>
                        <option value="price_asc" <?= $sortBy=='price_asc'?'selected':'' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= $sortBy=='price_desc'?'selected':'' ?>>Price: High to Low</option>
                    </select>
                </form>
            </div>
            
            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>No products found</h3>
                    <p>Try different filters or search terms</p>
                    <a href="products.php" class="btn-primary">View All Products</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $i => $product): 
                        $displayPrice = $product['sale_price'] ?: $product['price'];
                        $hasDiscount = !empty($product['sale_price']);
                        $discountPercent = $hasDiscount ? round((1 - $product['sale_price'] / $product['price']) * 100) : 0;
                    ?>
                        <div class="product-card" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 60 ?>">
                            <div class="product-img-wrap">
                                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     loading="lazy"
                                     onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=400&q=80'">
                                <div class="product-badge">
                                    <?php if ($hasDiscount): ?>
                                        <span class="badge badge-sale">-<?= $discountPercent ?>%</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions">
                                    <a href="product.php?id=<?= $product['id'] ?>" class="action-btn">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                                <h3 class="product-name">
                                    <a href="product.php?id=<?= $product['id'] ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </h3>
                                <div class="product-rating">
                                    <div class="stars">
                                        <?php for ($s=1;$s<=5;$s++): ?>
                                            <i class="<?= $s<=floor($product['rating'])?'fas':'far' ?> fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-count">(<?= number_format($product['reviews_count']) ?>)</span>
                                </div>
                                <div class="product-price">
                                    <span class="price-current"><?= CURRENCY ?> <?= number_format($displayPrice, 0) ?></span>
                                    <?php if ($hasDiscount): ?>
                                        <span class="price-original"><?= CURRENCY ?> <?= number_format($product['price'], 0) ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn-order-now"
                                    onclick="openOrderModal(
                                        <?= $product['id'] ?>,
                                        '<?= addslashes(htmlspecialchars($product['name'])) ?>',
                                        <?= $displayPrice ?>,
                                        <?= $product['price'] ?>,
                                        '<?= addslashes($product['image_url']) ?>',
                                        <?= $product['stock'] ?>
                                    )">
                                    <i class="fas fa-bolt"></i> Order Now
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($p = 1; $p <= $totalPages; $p++): 
                            $params_url = array_merge($_GET, ['page' => $p]);
                        ?>
                            <a href="?<?= http_build_query($params_url) ?>" 
                               class="page-btn <?= $p == $page ? 'active' : '' ?>">
                                <?= $p ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<style>
/* Products page specific styles */
.sidebar-card {
    background: white;
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.sidebar-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--gray-100);
    display: flex;
    align-items: center;
    gap: 8px;
}
.sidebar-list a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 10px;
    border-radius: var(--radius-sm);
    font-size: 0.88rem;
    color: var(--gray-600);
    transition: var(--transition);
    margin-bottom: 2px;
}
.sidebar-list a:hover, .sidebar-list a.active {
    background: rgba(99,102,241,0.08);
    color: var(--primary);
    font-weight: 600;
}
.count {
    background: var(--gray-100);
    color: var(--gray-600);
    font-size: 0.72rem;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
}
.sidebar-filter-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    font-size: 0.88rem;
    color: var(--gray-600);
    border: 2px solid var(--gray-100);
    transition: var(--transition);
    width: 100%;
}
.sidebar-filter-btn:hover, .sidebar-filter-btn.active {
    border-color: var(--danger);
    color: var(--danger);
    background: rgba(239,68,68,0.05);
}
.products-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    padding: 14px 20px;
    border-radius: var(--radius-sm);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}
.results-count { font-size: 0.9rem; color: var(--gray-600); }
.sort-form { display: flex; align-items: center; gap: 10px; }
.sort-select {
    padding: 8px 14px;
    border: 2px solid var(--gray-100);
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    outline: none;
    cursor: pointer;
    transition: var(--transition);
    color: var(--dark);
}
.sort-select:focus { border-color: var(--primary); }
.pagination {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-top: 40px;
    flex-wrap: wrap;
}
.page-btn {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--gray-600);
    background: white;
    border: 2px solid var(--gray-100);
    transition: var(--transition);
}
.page-btn:hover, .page-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
@media(max-width:768px){
    .sidebar { display: none; }
    div[style*="grid-template-columns:260px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
