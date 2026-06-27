<?php
/**
 * Shopping Cart Page
 * Cart items, totals, checkout button
 */

require_once 'config/database.php';
$pageTitle = 'Shopping Cart';

// Cart mein kya kya hai calculate karo
$cartItems = [];
$subtotal  = 0;

if (!empty($_SESSION['cart'])) {
    $db = getDB();
    foreach ($_SESSION['cart'] as $productId => $item) {
        // Har cart item ka latest product data lo
        $stmt = $db->prepare("SELECT id, name, price, sale_price, image_url, stock FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            $itemPrice = $product['sale_price'] ?: $product['price'];
            $itemTotal = $itemPrice * $item['quantity'];
            $subtotal += $itemTotal;
            $cartItems[] = [
                'id'        => $product['id'],
                'name'      => $product['name'],
                'price'     => $itemPrice,
                'image'     => $product['image_url'],
                'quantity'  => $item['quantity'],
                'item_total'=> $itemTotal
            ];
        }
    }
}

// Shipping calculate karo
$shippingFee = $subtotal >= 2000 ? 0 : 200;
$total       = $subtotal + $shippingFee;

require_once 'includes/header.php';
?>

<!-- Page Title -->
<div class="page-title-bar">
    <div class="container">
        <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Cart</span>
        </div>
    </div>
</div>

<div class="cart-page">
    <div class="container">
        
        <?php if (empty($cartItems)): ?>
            <!-- Khali cart -->
            <div class="empty-state" data-aos="fade-up">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty!</h3>
                <p>Looks like you haven't added anything to your cart yet.</p>
                <a href="products.php" class="btn-primary">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
            
        <?php else: ?>
            <div class="cart-layout">
                
                <!-- Cart Items Table -->
                <div>
                    <div class="cart-table">
                        <!-- Table Header -->
                        <div class="cart-table-header">
                            <div>Product</div>
                            <div>Price</div>
                            <div>Quantity</div>
                            <div>Total</div>
                            <div></div>
                        </div>
                        
                        <!-- Cart Items -->
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item" data-product-id="<?= $item['id'] ?>">
                                
                                <!-- Product info -->
                                <div class="cart-item-product">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=200&q=80'">
                                    <div>
                                        <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                        <a href="product.php?id=<?= $item['id'] ?>" class="cart-item-cat" style="color:var(--primary);">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Price -->
                                <div class="cart-item-price">
                                    <?= CURRENCY ?> <?= number_format($item['price'], 0) ?>
                                </div>
                                
                                <!-- Quantity Control -->
                                <div class="qty-control" data-product-id="<?= $item['id'] ?>">
                                    <button class="qty-btn" data-action="minus">−</button>
                                    <input type="number" class="qty-input" value="<?= $item['quantity'] ?>" min="1" max="99">
                                    <button class="qty-btn" data-action="plus">+</button>
                                </div>
                                
                                <!-- Item Total -->
                                <div class="cart-item-total">
                                    <?= CURRENCY ?> <?= number_format($item['item_total'], 0) ?>
                                </div>
                                
                                <!-- Remove Button -->
                                <button class="cart-remove-btn" data-product-id="<?= $item['id'] ?>" title="Remove item">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Continue Shopping -->
                    <div style="margin-top:20px; display:flex; gap:12px; flex-wrap:wrap;">
                        <a href="products.php" class="btn-primary" style="background:var(--gray-600);">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="cart-summary" data-aos="fade-left">
                    <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?= count($cartItems) ?> items)</span>
                        <span><?= CURRENCY ?> <?= number_format($subtotal, 0) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping Fee</span>
                        <span style="color:<?= $shippingFee == 0 ? 'var(--success)' : 'inherit' ?>">
                            <?= $shippingFee == 0 ? 'FREE' : CURRENCY . ' ' . number_format($shippingFee, 0) ?>
                        </span>
                    </div>
                    <?php if ($subtotal < 2000): ?>
                        <p style="font-size:0.8rem; color:var(--primary); margin:-8px 0 12px; background:rgba(99,102,241,0.08); padding:8px 12px; border-radius:6px;">
                            <i class="fas fa-info-circle"></i> 
                            Add <?= CURRENCY ?> <?= number_format(2000 - $subtotal, 0) ?> more for free shipping!
                        </p>
                    <?php endif; ?>
                    
                    <!-- Coupon Code -->
                    <div class="coupon-input">
                        <input type="text" placeholder="Coupon code..." id="couponInput">
                        <button class="btn-apply" onclick="applyCoupon()">Apply</button>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?= CURRENCY ?> <?= number_format($total, 0) ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn-checkout">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </a>
                    
                    <!-- Payment icons -->
                    <div style="text-align:center; margin-top:16px;">
                        <p style="font-size:0.78rem; color:var(--gray-400); margin-bottom:8px;">Secure Payment Methods</p>
                        <div style="display:flex; justify-content:center; gap:10px; font-size:1.4rem; color:var(--gray-400);">
                            <i class="fab fa-cc-visa" title="Visa"></i>
                            <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                            <i class="fas fa-money-bill-wave" title="Cash on Delivery"></i>
                            <i class="fas fa-mobile-alt" title="JazzCash"></i>
                        </div>
                    </div>
                </div>
                
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Coupon code apply karo
function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim().toUpperCase();
    if (code === 'SAVE20') {
        showToast('Coupon applied! 20% discount added.', 'success');
    } else if (code === '') {
        showToast('Please enter a coupon code', 'error');
    } else {
        showToast('Invalid coupon code. Try SAVE20', 'error');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
