<?php
/**
 * Homepage - ShopZone ka main page
 * Hero section, categories, featured products sab yahan
 */

$pageTitle = 'Welcome to ShopZone - Best Online Shopping';
require_once 'config/database.php';

$db = getDB();

// Featured products fetch karo (is_featured = 1)
$featuredProducts = $db->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.is_featured = 1 AND p.is_active = 1 
    ORDER BY p.created_at DESC 
    LIMIT 8
")->fetchAll();

// Saari categories fetch karo (count ke saath)
$categories = $db->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
    GROUP BY c.id
")->fetchAll();

// Sale products (sale_price hai) 
$saleProducts = $db->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.sale_price IS NOT NULL AND p.is_active = 1
    ORDER BY (p.price - p.sale_price) DESC
    LIMIT 4
")->fetchAll();

require_once 'includes/header.php';
?>

<!-- ============================================
     HERO SECTION - Main banner
     ============================================ -->
<section class="hero">
    <div class="container">
        <div class="hero-inner">
            
            <!-- Hero Text -->
            <div class="hero-text" data-aos="fade-right">
                <div class="hero-badge">
                    <i class="fas fa-fire"></i> Flash Sale - Up to 50% OFF
                </div>
                <h1>
                    Shop <span>Smarter,</span><br>
                    Live Better
                </h1>
                <p class="hero-desc">
                    Discover thousands of premium products at unbeatable prices. 
                    Fast delivery, easy returns, and 24/7 customer support.
                </p>
                <div class="hero-btns">
                    <a href="products.php" class="btn-hero-primary">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </a>
                    <a href="products.php?sale=1" class="btn-hero-secondary">
                        <i class="fas fa-tag"></i> View Deals
                    </a>
                </div>
                
                <!-- Stats Counter -->
                <div class="hero-stats">
                    <div class="hero-stat">
                        <h3 data-counter="50000" data-suffix="+">0+</h3>
                        <p>Happy Customers</p>
                    </div>
                    <div class="hero-stat">
                        <h3 data-counter="5000" data-suffix="+">0+</h3>
                        <p>Products</p>
                    </div>
                    <div class="hero-stat">
                        <h3 data-counter="100" data-suffix="%">0%</h3>
                        <p>Satisfaction</p>
                    </div>
                </div>
            </div>
            
            <!-- Hero Visual - Featured product cards -->
            <div class="hero-visual" data-aos="fade-left">
                <?php 
                // Top 4 featured products hero mein dikhao
                $heroProducts = array_slice($featuredProducts, 0, 4);
                foreach ($heroProducts as $hp): 
                    $displayPrice = $hp['sale_price'] ?: $hp['price'];
                ?>
                    <div class="hero-product-card">
                        <img src="<?= htmlspecialchars($hp['image_url']) ?>" 
                             alt="<?= htmlspecialchars($hp['name']) ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=300&q=80'">
                        <div class="hero-card-info">
                            <p><?= htmlspecialchars(substr($hp['name'], 0, 25)) ?>...</p>
                            <div class="hero-card-price"><?= CURRENCY ?> <?= number_format($displayPrice, 0) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        </div>
    </div>
</section>

<!-- ============================================
     FEATURES / TRUST BADGES
     ============================================ -->
<section class="section section-light">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-icon"><i class="fas fa-truck"></i></div>
                <h3>Free Delivery</h3>
                <p>Free shipping on all orders above Rs. 2,000 across Pakistan</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure Payment</h3>
                <p>100% secure transactions with encryption protection</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon"><i class="fas fa-undo"></i></div>
                <h3>Easy Returns</h3>
                <p>7-day hassle-free return policy on all products</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Round the clock customer support for all your queries</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     CATEGORIES SECTION
     ============================================ -->
<section class="section section-gray">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-tag"><i class="fas fa-th-large"></i> Browse By</div>
            <h2 class="section-title">Shop by <span>Category</span></h2>
            <p class="section-subtitle">Find exactly what you're looking for in our wide range of categories</p>
        </div>
        
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="products.php?category=<?= $cat['slug'] ?>" class="category-card" data-aos="zoom-in">
                    <div class="icon-wrap">
                        <i class="<?= $cat['icon'] ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    <p><?= $cat['product_count'] ?> Products</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
     FEATURED PRODUCTS
     ============================================ -->
<section class="section section-light">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-tag"><i class="fas fa-star"></i> Top Picks</div>
            <h2 class="section-title">Featured <span>Products</span></h2>
            <p class="section-subtitle">Handpicked premium products that our customers love the most</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($featuredProducts as $i => $product): 
                $displayPrice = $product['sale_price'] ?: $product['price'];
                $hasDiscount = !empty($product['sale_price']);
                $discountPercent = $hasDiscount ? round((1 - $product['sale_price'] / $product['price']) * 100) : 0;
            ?>
                <div class="product-card" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 80 ?>">
                    
                    <!-- Product Image -->
                    <div class="product-img-wrap">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=400&q=80'">
                        
                        <!-- Badges -->
                        <div class="product-badge">
                            <?php if ($hasDiscount): ?>
                                <span class="badge badge-sale">-<?= $discountPercent ?>%</span>
                            <?php endif; ?>
                            <?php if ($product['is_featured']): ?>
                                <span class="badge badge-featured">Featured</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Quick Action Buttons -->
                        <div class="product-actions">
                            <a href="product.php?id=<?= $product['id'] ?>" class="action-btn" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="action-btn" title="Add to Wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                        <h3 class="product-name">
                            <a href="product.php?id=<?= $product['id'] ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h3>
                        
                        <!-- Star Rating -->
                        <div class="product-rating">
                            <div class="stars">
                                <?php
                                // Rating ke hisaab se stars dikhao
                                $rating = floatval($product['rating']);
                                for ($s = 1; $s <= 5; $s++) {
                                    if ($s <= floor($rating)) echo '<i class="fas fa-star"></i>';
                                    elseif ($s - 0.5 <= $rating) echo '<i class="fas fa-star-half-alt"></i>';
                                    else echo '<i class="far fa-star"></i>';
                                }
                                ?>
                            </div>
                            <span class="rating-count">(<?= number_format($product['reviews_count']) ?>)</span>
                        </div>
                        
                        <!-- Price -->
                        <div class="product-price">
                            <span class="price-current"><?= CURRENCY ?> <?= number_format($displayPrice, 0) ?></span>
                            <?php if ($hasDiscount): ?>
                                <span class="price-original"><?= CURRENCY ?> <?= number_format($product['price'], 0) ?></span>
                                <span class="price-discount">Save <?= $discountPercent ?>%</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Order Now Button -->
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
        
        <!-- View All Button -->
        <div style="text-align:center; margin-top:48px;" data-aos="fade-up">
            <a href="products.php" class="btn-primary">
                <i class="fas fa-th-large"></i> View All Products
            </a>
        </div>
    </div>
</section>

<!-- ============================================
     PROMO BANNERS
     ============================================ -->
<section class="section section-gray">
    <div class="container">
        <div class="promo-grid" data-aos="fade-up">
            
            <!-- Big promo - Left side -->
            <div class="promo-card">
                <div class="promo-card-bg" style="background-image: url('https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&q=80')"></div>
                <div class="promo-card-overlay"></div>
                <div class="promo-card-content">
                    <span class="promo-tag">NEW ARRIVALS</span>
                    <h3>Summer Fashion<br>Collection 2025</h3>
                    <p>Get the latest trends up to 40% off</p>
                    <a href="products.php?category=fashion" class="btn-promo">
                        Shop Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <!-- Two small promos - Right side -->
            <div class="promo-grid-right">
                <div class="promo-card">
                    <div class="promo-card-bg" style="background-image: url('https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=500&q=80')"></div>
                    <div class="promo-card-overlay"></div>
                    <div class="promo-card-content">
                        <span class="promo-tag">DEAL OF DAY</span>
                        <h3>Electronics</h3>
                        <a href="products.php?category=electronics" class="btn-promo">
                            Explore <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="promo-card">
                    <div class="promo-card-bg" style="background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500&q=80')"></div>
                    <div class="promo-card-overlay"></div>
                    <div class="promo-card-content">
                        <span class="promo-tag">SALE 30% OFF</span>
                        <h3>Sports & Fitness</h3>
                        <a href="products.php?category=sports" class="btn-promo">
                            Shop <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     SALE PRODUCTS SECTION
     ============================================ -->
<section class="section section-light">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-tag" style="background:rgba(239,68,68,0.1);color:#ef4444">
                <i class="fas fa-fire"></i> Hot Deals
            </div>
            <h2 class="section-title"><span>Sale</span> Products</h2>
            <p class="section-subtitle">Grab these amazing deals before they're gone!</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($saleProducts as $i => $product): 
                $discountPercent = round((1 - $product['sale_price'] / $product['price']) * 100);
            ?>
                <div class="product-card" data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>">
                    <div class="product-img-wrap">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy"
                             onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=400&q=80'">
                        <div class="product-badge">
                            <span class="badge badge-sale">-<?= $discountPercent ?>% OFF</span>
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
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="<?= $s <= floor($product['rating']) ? 'fas' : 'far' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="product-price">
                            <span class="price-current"><?= CURRENCY ?> <?= number_format($product['sale_price'], 0) ?></span>
                            <span class="price-original"><?= CURRENCY ?> <?= number_format($product['price'], 0) ?></span>
                        </div>
                        <button class="btn-order-now"
                            onclick="openOrderModal(
                                <?= $product['id'] ?>,
                                '<?= addslashes(htmlspecialchars($product['name'])) ?>',
                                <?= $product['sale_price'] ?>,
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
    </div>
</section>

<!-- ============================================
     NEWSLETTER SECTION
     ============================================ -->
<section class="newsletter-section">
    <div class="container" data-aos="fade-up">
        <h2><i class="fas fa-envelope-open-text"></i> Stay in the Loop</h2>
        <p>Subscribe to our newsletter and get exclusive deals, new arrivals, and more!</p>
        <form class="newsletter-form" onsubmit="return handleNewsletter(event)">
            <input type="email" placeholder="Enter your email address..." required>
            <button type="submit">
                <i class="fas fa-paper-plane"></i> Subscribe
            </button>
        </form>
    </div>
</section>

<script>
// Newsletter form submit
function handleNewsletter(e) {
    e.preventDefault();
    showToast('Thank you for subscribing! 🎉', 'success');
    e.target.reset();
    return false;
}
</script>

<?php require_once 'includes/footer.php'; ?>
