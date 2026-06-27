<?php
/**
 * Order Now Modal + Rating Modal
 * Footer mein include karo - sab pages par kaam karega
 * JS se open hota hai jab "Order Now" click hota hai
 */
?>

<!-- ============================================
     ORDER NOW MODAL
     ============================================ -->
<div class="modal-overlay" id="orderModal">
    <div class="modal-box">
        
        <!-- Header -->
        <div class="modal-header">
            <h3><i class="fas fa-bolt"></i> Quick Order</h3>
            <button class="modal-close-btn" onclick="closeOrderModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Body -->
        <div class="modal-body">
            
            <!-- Product Preview (JS se fill hoga) -->
            <div class="modal-product-preview">
                <img id="modalProductImg" src="" alt="" 
                     onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=200&q=80'">
                <div class="modal-product-preview-info">
                    <h4 id="modalProductName">—</h4>
                    <div>
                        <span class="modal-product-preview-price" id="modalProductPrice">—</span>
                    </div>
                </div>
            </div>
            
            <!-- Quantity Row -->
            <div class="modal-qty-row">
                <label><i class="fas fa-sort-numeric-up" style="color:var(--primary);margin-right:6px;"></i>Quantity</label>
                <span class="modal-qty-total" id="modalTotalDisplay">—</span>
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeModalQty(-1)" type="button">−</button>
                    <input type="number" class="qty-input" id="modalQtyInput" value="1" min="1" max="99" 
                           oninput="updateModalTotal()">
                    <button class="qty-btn" onclick="changeModalQty(1)" type="button">+</button>
                </div>
            </div>
            
            <hr class="modal-divider">
            
            <!-- Customer Details Form -->
            <form id="orderNowForm" onsubmit="submitOrderNow(event)">
                <input type="hidden" id="modalProductId" name="product_id" value="">
                <input type="hidden" id="modalUnitPrice" value="">
                
                <div style="margin-bottom:16px;">
                    <p style="font-size:0.82rem; font-weight:700; color:var(--dark); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                        <i class="fas fa-user" style="color:var(--primary);"></i> Your Details
                    </p>
                    <div class="form-row-2" style="margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Full Name *</label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="customer_name" class="form-control"
                                       placeholder="Ahmad Ali" required
                                       value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>">
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label">Phone *</label>
                            <div class="input-icon-wrap">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" name="customer_phone" class="form-control"
                                       placeholder="+92 300 1234567" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label">Delivery Address *</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-home input-icon" style="top:14px;transform:none;"></i>
                            <textarea name="customer_address" class="form-control" rows="2"
                                      style="padding-left:42px;resize:none;" 
                                      placeholder="House No, Street, Area..." required></textarea>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">City *</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-map-marker-alt input-icon"></i>
                            <select name="customer_city" class="form-control" style="padding-left:42px;" required>
                                <option value="">— Select City —</option>
                                <option>Karachi</option>
                                <option>Lahore</option>
                                <option>Islamabad</option>
                                <option>Rawalpindi</option>
                                <option>Faisalabad</option>
                                <option>Multan</option>
                                <option>Peshawar</option>
                                <option>Quetta</option>
                                <option>Sialkot</option>
                                <option>Gujranwala</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <hr class="modal-divider">
                
                <!-- Payment Method -->
                <div>
                    <p style="font-size:0.82rem; font-weight:700; color:var(--dark); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
                        <i class="fas fa-credit-card" style="color:var(--primary);"></i> Payment Method
                    </p>
                    <div class="payment-cards">
                        <!-- Cash on Delivery - default selected -->
                        <label class="payment-card-label checked" id="pay_cod">
                            <input type="radio" name="payment_method" value="cod" checked 
                                   onchange="selectPayment(this)">
                            <i class="fas fa-money-bill-wave" style="color:#10b981;"></i> Cash on Delivery
                        </label>
                        <label class="payment-card-label" id="pay_jazzcash">
                            <input type="radio" name="payment_method" value="jazzcash"
                                   onchange="selectPayment(this)">
                            <i class="fas fa-mobile-alt" style="color:#ef4444;"></i> JazzCash
                        </label>
                        <label class="payment-card-label" id="pay_easypaisa">
                            <input type="radio" name="payment_method" value="easypaisa"
                                   onchange="selectPayment(this)">
                            <i class="fas fa-mobile-alt" style="color:#10b981;"></i> Easypaisa
                        </label>
                        <label class="payment-card-label" id="pay_bank">
                            <input type="radio" name="payment_method" value="bank"
                                   onchange="selectPayment(this)">
                            <i class="fas fa-university" style="color:#6366f1;"></i> Bank Transfer
                        </label>
                    </div>
                </div>
                
                <!-- Optional Notes -->
                <div class="form-group">
                    <label class="form-label" style="font-size:0.82rem;">
                        Special Instructions <span style="color:var(--gray-400);">(optional)</span>
                    </label>
                    <textarea name="notes" class="form-control" rows="2" 
                              placeholder="Any special delivery instructions..." 
                              style="resize:none;"></textarea>
                </div>
                
            </form><!-- form end -->
        </div>
        
        <!-- Footer -->
        <div class="modal-footer">
            <button type="submit" form="orderNowForm" class="btn-order-submit" id="orderSubmitBtn">
                <i class="fas fa-check-circle"></i> Confirm Order
            </button>
            <div class="modal-secure-note">
                <i class="fas fa-shield-alt" style="color:var(--success);"></i>
                Secure checkout &bull; Your data is safe
            </div>
        </div>
        
    </div>
</div>

<!-- ============================================
     ORDER SUCCESS MODAL
     ============================================ -->
<div class="modal-overlay" id="orderSuccessModal">
    <div class="modal-box order-success-modal" style="max-width:480px;">
        
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Order Confirmed!</h3>
            <button class="modal-close-btn" onclick="closeSuccessAndOpenRating()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="order-success-body">
            <div class="order-success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h3>Order Placed Successfully!</h3>
            <p style="color:var(--gray-600); font-size:0.9rem;">
                Shukria! Aapka order receive ho gaya hai. Hum jald hi aapko call karenge.
            </p>
            
            <div class="order-number-badge" id="successOrderNumber">SZ-XXXXXXXX</div>
            
            <!-- Order summary grid -->
            <div class="order-details-grid">
                <div class="order-detail-item">
                    <div class="label"><i class="fas fa-user"></i> Customer</div>
                    <div class="value" id="successCustomerName">—</div>
                </div>
                <div class="order-detail-item">
                    <div class="label"><i class="fas fa-phone"></i> Phone</div>
                    <div class="value" id="successPhone">—</div>
                </div>
                <div class="order-detail-item">
                    <div class="label"><i class="fas fa-map-marker-alt"></i> City</div>
                    <div class="value" id="successCity">—</div>
                </div>
                <div class="order-detail-item">
                    <div class="label"><i class="fas fa-money-bill"></i> Total</div>
                    <div class="value" id="successTotal" style="color:var(--primary);">—</div>
                </div>
                <div class="order-detail-item" style="grid-column:1/-1;">
                    <div class="label"><i class="fas fa-home"></i> Delivery Address</div>
                    <div class="value" id="successAddress">—</div>
                </div>
            </div>
            
            <!-- Delivery steps -->
            <div style="display:flex; justify-content:space-between; background:var(--gray-100); border-radius:var(--radius-sm); padding:14px 18px; margin:16px 0; gap:8px;">
                <?php
                $steps = [
                    ['fas fa-check', '#10b981', 'Confirmed'],
                    ['fas fa-box',   '#6366f1', 'Packing'],
                    ['fas fa-truck', '#f59e0b', 'Shipping'],
                    ['fas fa-home',  '#ec4899', 'Delivered'],
                ];
                foreach ($steps as $i => [$icon, $color, $label]):
                ?>
                    <div style="text-align:center; flex:1;">
                        <div style="width:34px;height:34px;border-radius:50%;background:<?= $color ?>22;display:flex;align-items:center;justify-content:center;margin:0 auto 6px;<?= $i==0 ? "background:$color;" : '' ?>">
                            <i class="<?= $icon ?>" style="font-size:0.85rem;color:<?= $i==0 ? 'white' : $color ?>;"></i>
                        </div>
                        <div style="font-size:0.7rem;font-weight:600;color:<?= $i==0 ? $color : 'var(--gray-400)' ?>;">
                            <?= $label ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button onclick="closeSuccessAndOpenRating()" class="btn-order-submit" style="margin-top:4px;">
                <i class="fas fa-star"></i> Rate This Product
            </button>
            <button onclick="skipRating()" class="btn-skip-rating">
                Skip — Go back to shopping
            </button>
        </div>
    </div>
</div>

<!-- ============================================
     RATING POPUP MODAL
     ============================================ -->
<div class="modal-overlay" id="ratingModal">
    <div class="modal-box rating-modal" style="max-width:440px;">
        
        <div class="modal-header">
            <h3><i class="fas fa-star"></i> Rate Your Purchase</h3>
            <button class="modal-close-btn" onclick="closeRatingModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="rating-modal-body">
            
            <!-- Product ki image aur naam -->
            <img id="ratingProductImg" class="rating-product-thumb" src="" alt=""
                 onerror="this.src='https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=200&q=80'">
            <h4 id="ratingProductName">—</h4>
            <p>Aapka experience kaisa raha? Stars par click karke rate karein!</p>
            
            <!-- Star selector -->
            <div class="star-selector" id="starSelector">
                <span class="star-pick" data-val="1" onclick="selectStar(1)">★</span>
                <span class="star-pick" data-val="2" onclick="selectStar(2)">★</span>
                <span class="star-pick" data-val="3" onclick="selectStar(3)">★</span>
                <span class="star-pick" data-val="4" onclick="selectStar(4)">★</span>
                <span class="star-pick" data-val="5" onclick="selectStar(5)">★</span>
            </div>
            <div class="rating-label" id="ratingLabel">Click a star to rate</div>
            
            <form id="ratingForm" onsubmit="submitRating(event)">
                <input type="hidden" id="ratingProductId" name="product_id" value="">
                <input type="hidden" id="ratingOrderId" name="order_id" value="">
                <input type="hidden" id="ratingValue" name="rating" value="0">
                
                <!-- Reviewer name -->
                <div class="form-group" style="text-align:left; margin-bottom:12px;">
                    <label class="form-label" style="font-size:0.82rem;">Your Name</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="reviewer_name" class="form-control"
                               placeholder="Ahmad Ali"
                               value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>"
                               required>
                    </div>
                </div>
                
                <!-- Review text -->
                <textarea name="review_text" class="review-textarea"
                          placeholder="Tell others about this product... (optional)"></textarea>
                
                <button type="submit" class="btn-submit-rating" id="ratingSubmitBtn">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </form>
            
            <button onclick="closeRatingModal()" class="btn-skip-rating">
                <i class="fas fa-times"></i> Skip for now
            </button>
            
        </div>
    </div>
</div>

<!-- ============================================
     JAVASCRIPT - Modal Logic
     ============================================ -->
<script>
// ============================================
// Global variables - order data store karo
// ============================================
let currentOrderData = null;   // Order submit hone ke baad data yahan store hoga
let selectedRating   = 0;       // Star rating selected value

const CURRENCY = '<?= CURRENCY ?>';

// ============================================
// ORDER NOW MODAL OPEN KARO
// product card ke "Order Now" button se call hota hai
// ============================================
function openOrderModal(productId, productName, productPrice, productOrigPrice, productImg, maxStock) {
    // Hidden fields mein data set karo
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalUnitPrice').value  = productPrice;
    document.getElementById('modalQtyInput').value   = 1;
    document.getElementById('modalQtyInput').max     = maxStock || 99;
    
    // Product preview update karo
    document.getElementById('modalProductName').textContent  = productName;
    document.getElementById('modalProductImg').src           = productImg;
    document.getElementById('modalProductPrice').innerHTML   = 
        CURRENCY + ' ' + formatNumber(productPrice) +
        (productOrigPrice > productPrice 
            ? ' <del>' + CURRENCY + ' ' + formatNumber(productOrigPrice) + '</del>'
            : '');
    
    // Total update karo
    updateModalTotal();
    
    // Modal dikhao
    document.getElementById('orderModal').classList.add('active');
    document.body.style.overflow = 'hidden'; // Background scroll band karo
}

// ============================================
// MODAL QUANTITY CHANGE
// ============================================
function changeModalQty(delta) {
    const input = document.getElementById('modalQtyInput');
    const max   = parseInt(input.max) || 99;
    let val     = parseInt(input.value) || 1;
    val         = Math.max(1, Math.min(max, val + delta));
    input.value = val;
    updateModalTotal();
}

function updateModalTotal() {
    const qty   = parseInt(document.getElementById('modalQtyInput').value) || 1;
    const price = parseFloat(document.getElementById('modalUnitPrice').value) || 0;
    const shipping = (price * qty) >= 2000 ? 0 : 200;
    const total = price * qty + shipping;
    
    document.getElementById('modalTotalDisplay').innerHTML = 
        CURRENCY + ' ' + formatNumber(total) +
        (shipping === 0 
            ? ' <span style="font-size:0.72rem;color:#10b981;">(Free shipping)</span>'
            : ' <span style="font-size:0.72rem;color:var(--gray-400);">(+200 shipping)</span>');
}

// ============================================
// ORDER FORM SUBMIT - AJAX
// ============================================
function submitOrderNow(e) {
    e.preventDefault();
    
    const form    = document.getElementById('orderNowForm');
    const btn     = document.getElementById('orderSubmitBtn');
    const qty     = document.getElementById('modalQtyInput').value;
    
    // Form data + quantity collect karo
    const formData = new FormData(form);
    formData.append('action', 'place_order');
    formData.append('quantity', qty);
    
    // Loading state
    btn.disabled   = true;
    btn.innerHTML  = '<i class="fas fa-spinner fa-spin"></i> Placing Order...';
    
    fetch('<?= SITE_URL ?>/ajax/order.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Order';
        
        if (data.success) {
            // Order data save karo - rating modal ke liye bhi chahiye
            currentOrderData = data;
            
            // Order modal band karo
            document.getElementById('orderModal').classList.remove('active');
            
            // Success modal fill karo
            const form_name    = form.querySelector('[name="customer_name"]').value;
            const form_phone   = form.querySelector('[name="customer_phone"]').value;
            const form_city    = form.querySelector('[name="customer_city"]').value;
            const form_address = form.querySelector('[name="customer_address"]').value;
            const total        = parseFloat(document.getElementById('modalUnitPrice').value) * parseInt(qty);
            const shipping     = total >= 2000 ? 0 : 200;
            
            document.getElementById('successOrderNumber').textContent = data.order_number;
            document.getElementById('successCustomerName').textContent = form_name;
            document.getElementById('successPhone').textContent        = form_phone;
            document.getElementById('successCity').textContent         = form_city;
            document.getElementById('successAddress').textContent      = form_address + ', ' + form_city;
            document.getElementById('successTotal').textContent        = 
                CURRENCY + ' ' + formatNumber(total + shipping);
            
            // Success modal dikhao
            document.getElementById('orderSuccessModal').classList.add('active');
            
            // Form reset karo
            form.reset();
            
        } else {
            showToast(data.message || 'Order failed. Please try again.', 'error');
        }
    })
    .catch(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Order';
        showToast('Connection error. Please try again.', 'error');
    });
}

// ============================================
// SUCCESS MODAL CLOSE + RATING OPEN
// ============================================
function closeSuccessAndOpenRating() {
    document.getElementById('orderSuccessModal').classList.remove('active');
    
    if (currentOrderData) {
        openRatingModal(
            currentOrderData.product_id,
            currentOrderData.order_id,
            currentOrderData.product_name,
            currentOrderData.product_img
        );
    } else {
        document.body.style.overflow = '';
    }
}

function skipRating() {
    document.getElementById('orderSuccessModal').classList.remove('active');
    document.body.style.overflow = '';
    currentOrderData = null;
    showToast('Order placed! Happy shopping! 🛍️', 'success');
}

// ============================================
// RATING MODAL OPEN KARO
// ============================================
function openRatingModal(productId, orderId, productName, productImg) {
    document.getElementById('ratingProductId').value  = productId;
    document.getElementById('ratingOrderId').value    = orderId;
    document.getElementById('ratingProductName').textContent = productName;
    document.getElementById('ratingProductImg').src   = productImg;
    document.getElementById('ratingValue').value      = 0;
    selectedRating = 0;
    
    // Stars reset karo
    document.querySelectorAll('.star-pick').forEach(s => s.classList.remove('selected', 'hovered'));
    document.getElementById('ratingLabel').textContent = 'Click a star to rate';
    
    document.getElementById('ratingModal').classList.add('active');
}

// ============================================
// STAR SELECTION
// ============================================
const starLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent!'];

function selectStar(val) {
    selectedRating = val;
    document.getElementById('ratingValue').value = val;
    document.getElementById('ratingLabel').textContent = starLabels[val] + ' (' + val + '/5)';
    
    document.querySelectorAll('.star-pick').forEach(s => {
        const starVal = parseInt(s.dataset.val);
        s.classList.toggle('selected', starVal <= val);
        s.classList.remove('hovered');
    });
}

// Star hover effects
document.querySelectorAll('.star-pick').forEach(star => {
    star.addEventListener('mouseenter', function() {
        const hoverVal = parseInt(this.dataset.val);
        document.querySelectorAll('.star-pick').forEach(s => {
            s.classList.toggle('hovered', parseInt(s.dataset.val) <= hoverVal);
        });
        document.getElementById('ratingLabel').textContent = starLabels[hoverVal];
    });
    star.addEventListener('mouseleave', function() {
        document.querySelectorAll('.star-pick').forEach(s => s.classList.remove('hovered'));
        if (selectedRating > 0) {
            document.getElementById('ratingLabel').textContent = 
                starLabels[selectedRating] + ' (' + selectedRating + '/5)';
        } else {
            document.getElementById('ratingLabel').textContent = 'Click a star to rate';
        }
    });
});

// ============================================
// RATING FORM SUBMIT
// ============================================
function submitRating(e) {
    e.preventDefault();
    
    if (selectedRating === 0) {
        showToast('Please select a star rating!', 'error');
        document.getElementById('starSelector').style.animation = 'shake 0.4s ease';
        setTimeout(() => document.getElementById('starSelector').style.animation = '', 400);
        return;
    }
    
    const btn      = document.getElementById('ratingSubmitBtn');
    const formData = new FormData(document.getElementById('ratingForm'));
    formData.append('action', 'submit_review');
    
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    fetch('<?= SITE_URL ?>/ajax/order.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
        
        if (data.success) {
            closeRatingModal();
            showToast('Thank you for your review! ⭐', 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
        showToast('Could not submit review.', 'error');
    });
}

// ============================================
// MODAL CLOSE FUNCTIONS
// ============================================
function closeOrderModal() {
    document.getElementById('orderModal').classList.remove('active');
    document.body.style.overflow = '';
}

function closeRatingModal() {
    document.getElementById('ratingModal').classList.remove('active');
    document.body.style.overflow = '';
    currentOrderData = null;
}

// Overlay click karne par band karo
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        // Sirf overlay click par band karo, modal box click nahi
        if (e.target === this) {
            this.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// ESC key se band karo
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => {
            m.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
});

// ============================================
// PAYMENT METHOD SELECTION
// ============================================
function selectPayment(radio) {
    document.querySelectorAll('.payment-card-label').forEach(l => l.classList.remove('checked'));
    radio.closest('.payment-card-label').classList.add('checked');
}

// ============================================
// NUMBER FORMAT HELPER
// ============================================
function formatNumber(n) {
    return parseInt(n).toLocaleString('en-PK');
}

// CSS shake animation for star selector
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `
@keyframes shake {
    0%,100%{transform:translateX(0)}
    20%{transform:translateX(-8px)}
    40%{transform:translateX(8px)}
    60%{transform:translateX(-4px)}
    80%{transform:translateX(4px)}
}`;
document.head.appendChild(shakeStyle);
</script>
