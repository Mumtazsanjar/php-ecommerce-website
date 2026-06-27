<?php
/**
 * Product Detail Page
 * Single product ki poori details
 */

require_once 'config/database.php';
$db = getDB();

// Product ID URL se nikalo
$productId = (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

// Product details fetch karo
$stmt = $db->prepare("
    SELECT p.*, c.name as category_name, c.slug as category_slug
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ? AND p.is_active = 1
");
$stmt->execute([$productId]);
$product = $stmt->fetch();

// Product nahi mila toh redirect karo
if (!$product) {
    header('Location: products.php');
    exit;
}

// Related products fetch karo (same category ke)
$relatedStmt = $db->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
    LIMIT 4
");
$relatedStmt->execute([$product['category_id'], $productId]);
$relatedProducts = $relatedStmt->fetchAll();

$displayPrice = $product['sale_price'] ?: $product['price'];
$hasDiscount  = !empty($product['sale_price']);
$discountPct  = $hasDiscount ? round((1 - $product['sale_price'] / $product['price']) * 100) : 0;

$pageTitle = $product['name'];
require_once 'includes/header.php';
?>

<!-- Page Title Bar -->
<div class="page-title-bar">
    <div class="container">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="products.php">Products</a>
            <i class="fas fa-chevron-right"></i>
            <a href="products.php?category=<?= $product['category_slug'] ?>"><?= htmlspecialchars($product['category_name']) ?></a>
            <i class="fas fa-chevron-right"></i>
            <span><?= htmlspecialchars(substr($product['name'], 0, 30)) ?>...</span>
        </div>
    </div>
</div>

<div class="product-detail">
    <div class="container">
        <div class="product-detail-layout" data-aos="fade-up">
            
            <!-- Product Images -->
            <div class="product-gallery">
                <div class="product-gallery-main">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         id="mainProductImg"
                         onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&q=80'">
                </div>
                <?php if ($hasDiscount): ?>
                    <div style="margin-top:12px;">
                        <span class="badge badge-sale" style="font-size:0.9rem; padding:8px 16px;">
                            <i class="fas fa-tag"></i> <?= $discountPct ?>% OFF - Save <?= CURRENCY ?> <?= number_format($product['price'] - $product['sale_price'], 0) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="product-detail-info">
                
                <div class="product-detail-cat">
                    <i class="fas fa-tag"></i> <?= htmlspecialchars($product['category_name']) ?>
                </div>
                
                <h1 class="product-detail-name"><?= htmlspecialchars($product['name']) ?></h1>
                
                <!-- Rating -->
                <div class="product-detail-rating">
                    <div class="stars">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="<?= $s <= floor($product['rating']) ? 'fas' : 'far' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <span style="font-weight:600; color:var(--dark);"><?= $product['rating'] ?></span>
                    <span style="color:var(--gray-400); font-size:0.9rem;">(<?= number_format($product['reviews_count']) ?> reviews)</span>
                    <?php if ($product['stock'] > 0): ?>
                        <span style="color:var(--success); font-size:0.85rem; font-weight:600; margin-left:10px;">
                            <i class="fas fa-check-circle"></i> In Stock (<?= $product['stock'] ?> left)
                        </span>
                    <?php else: ?>
                        <span style="color:var(--danger); font-size:0.85rem; font-weight:600; margin-left:10px;">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Price -->
                <div class="product-detail-price">
                    <div>
                        <span class="price-big"><?= CURRENCY ?> <?= number_format($displayPrice, 0) ?></span>
                        <?php if ($hasDiscount): ?>
                            <span class="price-old"><?= CURRENCY ?> <?= number_format($product['price'], 0) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($hasDiscount): ?>
                        <span class="price-save">
                            You save <?= CURRENCY ?> <?= number_format($product['price'] - $product['sale_price'], 0) ?> (<?= $discountPct ?>% off)
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <p class="product-desc"><?= htmlspecialchars($product['description']) ?></p>
                
                <!-- Quantity & Add to Cart -->
                <!-- Quantity selector (modal mein bhi hoga, yeh sirf display ke liye) -->
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                    <div class="qty-control" style="border:2px solid var(--gray-100); border-radius:var(--radius-sm);">
                        <button class="qty-btn" onclick="changeDetailQty(-1)" type="button">−</button>
                        <input type="number" class="qty-input" id="productQty" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button class="qty-btn" onclick="changeDetailQty(1)" type="button">+</button>
                    </div>
                    <span style="font-size:0.82rem; color:var(--gray-400);">Max: <?= $product['stock'] ?> available</span>
                </div>
                
                <div class="product-detail-actions">
                    <?php if ($product['stock'] > 0): ?>
                        <button class="btn-cart-lg btn-order-now"
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
                    <?php else: ?>
                        <button class="btn-cart-lg" disabled style="opacity:0.5; cursor:not-allowed;">
                            <i class="fas fa-times-circle"></i> Out of Stock
                        </button>
                    <?php endif; ?>
                    <button class="btn-wishlist" title="Add to Wishlist">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                
                <!-- Product meta info -->
                <div style="background:var(--gray-100); border-radius:var(--radius-sm); padding:16px; margin-top:16px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--gray-600);">
                            <i class="fas fa-truck" style="color:var(--success)"></i>
                            <span>Free delivery over Rs. 2,000</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--gray-600);">
                            <i class="fas fa-shield-alt" style="color:var(--primary)"></i>
                            <span>100% Genuine Product</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--gray-600);">
                            <i class="fas fa-undo" style="color:var(--accent)"></i>
                            <span>7-day easy return</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--gray-600);">
                            <i class="fas fa-lock" style="color:var(--secondary)"></i>
                            <span>Secure payment</span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- Customer Reviews Section -->
        <?php
        // Is product ke reviews fetch karo
        $reviewsStmt = $db->prepare("
            SELECT * FROM product_reviews 
            WHERE product_id = ? 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        $reviewsStmt->execute([$productId]);
        $reviews = $reviewsStmt->fetchAll();
        ?>
        <div style="margin-top:70px;" data-aos="fade-up">
            <div class="section-header" style="text-align:left; margin-bottom:28px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 class="section-title" style="margin-bottom:4px;">Customer <span>Reviews</span></h2>
                    <p style="color:var(--gray-600); font-size:0.9rem;">
                        <?= count($reviews) ?> reviews &bull;
                        <span style="color:var(--accent);">
                            <?php for ($s=1;$s<=5;$s++): ?>
                                <i class="fas fa-star" style="font-size:0.85rem;"></i>
                            <?php endfor; ?>
                        </span>
                        <strong><?= $product['rating'] ?>/5</strong>
                    </p>
                </div>
                <button class="btn-primary" style="padding:10px 20px; font-size:0.85rem;"
                    onclick="openRatingModal(<?= $productId ?>, 0, '<?= addslashes(htmlspecialchars($product['name'])) ?>', '<?= addslashes($product['image_url']) ?>')">
                    <i class="fas fa-star"></i> Write a Review
                </button>
            </div>
            
            <?php if (empty($reviews)): ?>
                <div style="background:white; border-radius:var(--radius); padding:40px; text-align:center; box-shadow:0 2px 12px rgba(0,0,0,0.05);">
                    <i class="fas fa-star" style="font-size:2.5rem; color:var(--gray-100); display:block; margin-bottom:12px;"></i>
                    <p style="color:var(--gray-600);">No reviews yet. Be the first to review this product!</p>
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <?php foreach ($reviews as $review): ?>
                        <div style="background:white; border-radius:var(--radius); padding:20px 24px; box-shadow:0 2px 12px rgba(0,0,0,0.05); border-left:4px solid var(--accent);">
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <!-- Avatar -->
                                    <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:1rem;flex-shrink:0;">
                                        <?= strtoupper(substr($review['reviewer_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:var(--dark);">
                                            <?= htmlspecialchars($review['reviewer_name']) ?>
                                            <?php if ($review['is_verified']): ?>
                                                <span style="font-size:0.72rem; background:rgba(16,185,129,0.1); color:var(--success); padding:2px 8px; border-radius:20px; margin-left:6px; font-weight:600;">
                                                    <i class="fas fa-check-circle"></i> Verified
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display:flex; gap:2px; margin-top:3px;">
                                            <?php for ($s=1;$s<=5;$s++): ?>
                                                <i class="<?= $s<=$review['rating']?'fas':'far' ?> fa-star" 
                                                   style="color:var(--accent); font-size:0.78rem;"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <span style="font-size:0.78rem; color:var(--gray-400);">
                                    <i class="fas fa-clock"></i> <?= date('d M Y', strtotime($review['created_at'])) ?>
                                </span>
                            </div>
                            <?php if (!empty($review['review_text'])): ?>
                                <p style="margin-top:12px; color:var(--gray-600); font-size:0.9rem; line-height:1.7; padding-left:56px;">
                                    "<?= htmlspecialchars($review['review_text']) ?>"
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
            <div style="margin-top:80px;" data-aos="fade-up">
                <div class="section-header" style="text-align:left; margin-bottom:32px;">
                    <h2 class="section-title">Related <span>Products</span></h2>
                </div>
                <div class="products-grid">
                    <?php foreach ($relatedProducts as $rp): 
                        $rpPrice = $rp['sale_price'] ?: $rp['price'];
                    ?>
                        <div class="product-card">
                            <div class="product-img-wrap">
                                <img src="<?= htmlspecialchars($rp['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($rp['name']) ?>"
                                     loading="lazy"
                                     onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=400&q=80'">
                                <div class="product-actions">
                                    <a href="product.php?id=<?= $rp['id'] ?>" class="action-btn">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">
                                    <a href="product.php?id=<?= $rp['id'] ?>"><?= htmlspecialchars($rp['name']) ?></a>
                                </h3>
                                <div class="product-price">
                                    <span class="price-current"><?= CURRENCY ?> <?= number_format($rpPrice, 0) ?></span>
                                </div>
                                <button class="btn-order-now"
                                    onclick="openOrderModal(
                                        <?= $rp['id'] ?>,
                                        '<?= addslashes(htmlspecialchars($rp['name'])) ?>',
                                        <?= $rpPrice ?>,
                                        <?= $rp['price'] ?>,
                                        '<?= addslashes($rp['image_url']) ?>',
                                        <?= $rp['stock'] ?>
                                    )">
                                    <i class="fas fa-bolt"></i> Order Now
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<script>
// Product detail page quantity buttons
function changeDetailQty(delta) {
    const input = document.getElementById('productQty');
    const max   = parseInt(input.max) || 99;
    let val     = parseInt(input.value) || 1;
    val         = Math.max(1, Math.min(max, val + delta));
    input.value = val;
}
</script>

<?php require_once 'includes/footer.php'; ?>
