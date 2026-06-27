<?php
/**
 * Checkout Page
 * Shipping address aur payment method select karna
 */

require_once 'config/database.php';

// Cart khali hai?
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$db = getDB();

// Cart items calculate karo
$cartItems = [];
$subtotal  = 0;

foreach ($_SESSION['cart'] as $productId => $item) {
    $stmt = $db->prepare("SELECT id, name, price, sale_price, image_url FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if ($product) {
        $itemPrice = $product['sale_price'] ?: $product['price'];
        $subtotal += $itemPrice * $item['quantity'];
        $cartItems[] = array_merge($product, ['quantity' => $item['quantity'], 'item_price' => $itemPrice]);
    }
}

$shippingFee = $subtotal >= 2000 ? 0 : 200;
$total       = $subtotal + $shippingFee;

// Order place karo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['full_name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city    = trim($_POST['city'] ?? '');
    $payment = $_POST['payment_method'] ?? 'cod';
    
    if ($name && $phone && $address && $city) {
        // Unique order number banao
        $orderNumber = 'SZ-' . strtoupper(uniqid());
        $fullAddress = "$name, $address, $city, Phone: $phone";
        
        // Order table mein insert karo
        $orderStmt = $db->prepare("
            INSERT INTO orders (user_id, order_number, total_amount, status, payment_method, shipping_address)
            VALUES (?, ?, ?, 'pending', ?, ?)
        ");
        $orderStmt->execute([
            $_SESSION['user_id'] ?? null,
            $orderNumber,
            $total,
            $payment,
            $fullAddress
        ]);
        $orderId = $db->lastInsertId();
        
        // Order items insert karo
        $itemStmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $itemStmt->execute([$orderId, $item['id'], $item['quantity'], $item['item_price']]);
        }
        
        // Cart clear karo
        $_SESSION['cart'] = [];
        
        // Success page par bhejo
        $_SESSION['flash_message'] = "Order placed successfully! Order #$orderNumber";
        $_SESSION['flash_type']    = 'success';
        header("Location: order-success.php?order=$orderNumber");
        exit;
    }
}

$pageTitle = 'Checkout';
require_once 'includes/header.php';
?>

<div class="page-title-bar">
    <div class="container">
        <h1><i class="fas fa-credit-card"></i> Checkout</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="cart.php">Cart</a>
            <i class="fas fa-chevron-right"></i>
            <span>Checkout</span>
        </div>
    </div>
</div>

<div style="padding:50px 0 80px;">
    <div class="container">
        <form method="POST">
            <div style="display:grid; grid-template-columns:1fr 380px; gap:32px; align-items:start;">
                
                <!-- Left - Shipping & Payment -->
                <div>
                    
                    <!-- Shipping Details -->
                    <div class="admin-card" style="margin-bottom:24px;">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title"><i class="fas fa-map-marker-alt"></i> Shipping Details</h3>
                        </div>
                        <div style="padding:28px;">
                            
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                                <div class="form-group">
                                    <label class="form-label">Full Name *</label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="full_name" class="form-control" 
                                               placeholder="Ahmad Ali"
                                               value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Phone Number *</label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="tel" name="phone" class="form-control" 
                                               placeholder="+92 300 1234567"
                                               required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Full Address *</label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-home input-icon" style="top:16px; transform:none;"></i>
                                    <textarea name="address" class="form-control" rows="3" 
                                              placeholder="House/Flat No, Street, Area..."
                                              style="padding-left:42px; resize:vertical;" required></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">City *</label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-city input-icon"></i>
                                    <select name="city" class="form-control" style="padding-left:42px;" required>
                                        <option value="">Select City</option>
                                        <option value="Karachi">Karachi</option>
                                        <option value="Lahore">Lahore</option>
                                        <option value="Islamabad">Islamabad</option>
                                        <option value="Rawalpindi">Rawalpindi</option>
                                        <option value="Faisalabad">Faisalabad</option>
                                        <option value="Peshawar">Peshawar</option>
                                        <option value="Quetta">Quetta</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title"><i class="fas fa-credit-card"></i> Payment Method</h3>
                        </div>
                        <div style="padding:28px; display:flex; flex-direction:column; gap:14px;">
                            
                            <?php 
                            $paymentOptions = [
                                ['cod',        'fas fa-money-bill-wave', 'Cash on Delivery',    'Pay when your order arrives'],
                                ['jazzcash',   'fas fa-mobile-alt',      'JazzCash',            'Pay via JazzCash mobile wallet'],
                                ['easypaisa',  'fas fa-mobile-alt',      'Easypaisa',           'Pay via Easypaisa mobile wallet'],
                                ['bank',       'fas fa-university',      'Bank Transfer',       'Direct bank transfer'],
                            ];
                            foreach ($paymentOptions as [$val, $icon, $label, $desc]):
                            ?>
                            <label style="display:flex; align-items:center; gap:14px; padding:16px; border:2px solid var(--gray-100); border-radius:var(--radius-sm); cursor:pointer; transition:var(--transition);" 
                                   class="payment-option">
                                <input type="radio" name="payment_method" value="<?= $val ?>" 
                                       <?= $val=='cod'?'checked':'' ?> style="width:18px;height:18px; accent-color:var(--primary);">
                                <i class="<?= $icon ?>" style="font-size:1.4rem; color:var(--primary); width:28px; text-align:center;"></i>
                                <div>
                                    <div style="font-weight:600; color:var(--dark); font-size:0.95rem;"><?= $label ?></div>
                                    <div style="font-size:0.8rem; color:var(--gray-400);"><?= $desc ?></div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Right - Order Summary -->
                <div class="cart-summary" style="position:sticky; top:100px;">
                    <h3><i class="fas fa-receipt"></i> Your Order</h3>
                    
                    <!-- Items list -->
                    <div style="margin-bottom:20px; max-height:280px; overflow-y:auto; padding-right:4px;">
                        <?php foreach ($cartItems as $item): ?>
                            <div style="display:flex; gap:12px; align-items:center; padding:10px 0; border-bottom:1px solid var(--gray-100);">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     style="width:50px; height:50px; object-fit:cover; border-radius:8px; flex-shrink:0;"
                                     onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=100&q=80'">
                                <div style="flex:1;">
                                    <div style="font-size:0.85rem; font-weight:600; color:var(--dark);">
                                        <?= htmlspecialchars(substr($item['name'], 0, 28)) ?>
                                    </div>
                                    <div style="font-size:0.78rem; color:var(--gray-400);">Qty: <?= $item['quantity'] ?></div>
                                </div>
                                <div style="font-weight:700; color:var(--primary); font-size:0.9rem; white-space:nowrap;">
                                    <?= CURRENCY ?> <?= number_format($item['item_price'] * $item['quantity'], 0) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-row"><span>Subtotal</span><span><?= CURRENCY ?> <?= number_format($subtotal, 0) ?></span></div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span style="color:<?= $shippingFee==0?'var(--success)':'inherit' ?>">
                            <?= $shippingFee==0?'FREE':CURRENCY.' '.number_format($shippingFee,0) ?>
                        </span>
                    </div>
                    <div class="summary-row total">
                        <span>Grand Total</span>
                        <span><?= CURRENCY ?> <?= number_format($total, 0) ?></span>
                    </div>
                    
                    <button type="submit" class="btn-checkout">
                        <i class="fas fa-check-circle"></i> Place Order
                    </button>
                </div>
                
            </div>
        </form>
    </div>
</div>

<script>
// Payment option click par border highlight karo
document.querySelectorAll('.payment-option').forEach(label => {
    label.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(l => 
            l.style.borderColor = 'var(--gray-100)'
        );
        this.style.borderColor = 'var(--primary)';
        this.style.background = 'rgba(99,102,241,0.04)';
    });
});
// Default select COD
document.querySelector('.payment-option').style.borderColor = 'var(--primary)';
</script>

<?php require_once 'includes/footer.php'; ?>
