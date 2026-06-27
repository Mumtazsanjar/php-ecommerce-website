<?php
/**
 * Order Success Page
 * Order successfully place hone ke baad dikhao
 */

require_once 'config/database.php';

$orderNumber = $_GET['order'] ?? '';
$pageTitle   = 'Order Placed Successfully!';

require_once 'includes/header.php';
?>

<div style="padding:80px 0; text-align:center; min-height:60vh; display:flex; align-items:center;">
    <div class="container">
        
        <!-- Success Animation -->
        <div style="width:100px; height:100px; background:linear-gradient(135deg,var(--success),#059669); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 28px; box-shadow:0 8px 30px rgba(16,185,129,0.3);" data-aos="zoom-in">
            <i class="fas fa-check" style="font-size:2.5rem; color:white;"></i>
        </div>
        
        <h1 style="font-size:2.2rem; font-weight:800; color:var(--dark); margin-bottom:12px;" data-aos="fade-up">
            Order Placed Successfully!
        </h1>
        
        <p style="color:var(--gray-600); font-size:1.05rem; max-width:500px; margin:0 auto 20px;" data-aos="fade-up" data-aos-delay="100">
            Thank you for shopping with us! We've received your order and will process it soon.
        </p>
        
        <?php if ($orderNumber): ?>
            <div style="background:var(--gray-100); display:inline-block; padding:12px 28px; border-radius:var(--radius-sm); margin-bottom:36px;" data-aos="fade-up" data-aos-delay="150">
                <span style="color:var(--gray-600); font-size:0.9rem;">Order Number: </span>
                <strong style="color:var(--primary); font-size:1rem;"><?= htmlspecialchars($orderNumber) ?></strong>
            </div>
        <?php endif; ?>
        
        <!-- What Happens Next -->
        <div style="display:flex; justify-content:center; gap:24px; flex-wrap:wrap; margin-bottom:48px;" data-aos="fade-up" data-aos-delay="200">
            <?php
            $steps = [
                ['fas fa-check-circle', '#10b981', 'Order Confirmed', 'Your order has been received'],
                ['fas fa-box',          '#6366f1', 'Being Packed',    'We are packing your items'],
                ['fas fa-truck',        '#f59e0b', 'On the Way',      'Estimated 3-5 business days'],
                ['fas fa-home',         '#ec4899', 'Delivered',       'Enjoy your purchase!']
            ];
            foreach ($steps as $i => [$icon, $color, $title, $desc]):
            ?>
                <div style="text-align:center; width:160px;">
                    <div style="width:56px; height:56px; background:<?= $color ?>22; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                        <i class="<?= $icon ?>" style="color:<?= $color ?>; font-size:1.3rem;"></i>
                    </div>
                    <div style="font-weight:700; color:var(--dark); font-size:0.9rem;"><?= $title ?></div>
                    <div style="font-size:0.78rem; color:var(--gray-400); margin-top:4px;"><?= $desc ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;" data-aos="fade-up" data-aos-delay="300">
            <a href="index.php" class="btn-primary">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="orders.php" class="btn-primary" style="background:var(--gray-600);">
                    <i class="fas fa-box"></i> View My Orders
                </a>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
