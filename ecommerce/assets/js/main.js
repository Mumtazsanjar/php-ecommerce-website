/**
 * ShopZone - Main JavaScript File
 * Saari interactive functionality yahan hai
 */

// ============================================
// HEADER - Scroll par shadow badlna
// ============================================
const header = document.getElementById('mainHeader');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        header?.classList.add('scrolled');
    } else {
        header?.classList.remove('scrolled');
    }
});

// ============================================
// MOBILE MENU - Toggle
// ============================================
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mainNav = document.getElementById('mainNav');

mobileMenuBtn?.addEventListener('click', () => {
    mainNav.classList.toggle('open');
    const icon = mobileMenuBtn.querySelector('i');
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-times');
});

// ============================================
// BACK TO TOP BUTTON
// ============================================
const backToTop = document.getElementById('backToTop');

window.addEventListener('scroll', () => {
    if (window.scrollY > 400) {
        backToTop?.classList.add('show');
    } else {
        backToTop?.classList.remove('show');
    }
});

backToTop?.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// ============================================
// ADD TO CART - AJAX se cart mein dalna
// ============================================
document.addEventListener('click', function(e) {
    
    // "Add to Cart" button click
    if (e.target.closest('.btn-add-cart') || e.target.closest('.btn-cart-lg')) {
        const btn = e.target.closest('.btn-add-cart') || e.target.closest('.btn-cart-lg');
        const productId = btn.dataset.productId;
        
        if (!productId) return;
        
        // Loading state dikhao
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        btn.disabled = true;
        
        // AJAX request bhejo
        fetch('ajax/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=add&product_id=${productId}&quantity=1`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Cart badge update karo
                updateCartBadge(data.cart_count);
                // Success animation
                btn.innerHTML = '<i class="fas fa-check"></i> Added!';
                btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.style.background = '';
                    btn.disabled = false;
                }, 2000);
                showToast('Product added to cart!', 'success');
            } else {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
                showToast(data.message || 'Could not add to cart', 'error');
            }
        })
        .catch(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            showToast('Something went wrong', 'error');
        });
    }
});

// ============================================
// CART BADGE UPDATE KARO
// ============================================
function updateCartBadge(count) {
    let badge = document.querySelector('.cart-badge');
    const cartBtn = document.querySelector('.cart-btn');
    
    if (count > 0) {
        if (!badge) {
            // Badge nahi hai, naya banao
            badge = document.createElement('span');
            badge.className = 'cart-badge';
            cartBtn?.appendChild(badge);
        }
        badge.textContent = count;
    } else if (badge) {
        badge.remove();
    }
}

// ============================================
// TOAST NOTIFICATION - Chhota popup message
// ============================================
function showToast(message, type = 'success') {
    // Purana toast hatao
    const oldToast = document.querySelector('.toast-notification');
    if (oldToast) oldToast.remove();
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Toast styles
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '90px',
        right: '30px',
        background: type === 'success' ? '#10b981' : '#ef4444',
        color: 'white',
        padding: '14px 22px',
        borderRadius: '12px',
        boxShadow: '0 4px 20px rgba(0,0,0,0.2)',
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
        fontSize: '0.9rem',
        fontWeight: '600',
        zIndex: '9999',
        transform: 'translateY(20px)',
        opacity: '0',
        transition: 'all 0.3s ease',
        fontFamily: 'Inter, sans-serif'
    });
    
    document.body.appendChild(toast);
    
    // Animation
    requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
    });
    
    // 3 second baad hatao
    setTimeout(() => {
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================
// QUANTITY CONTROLS (Cart page par)
// ============================================
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.qty-btn');
    if (!btn) return;
    
    const input = btn.closest('.qty-control').querySelector('.qty-input');
    let val = parseInt(input.value) || 1;
    
    if (btn.dataset.action === 'plus') {
        val = Math.min(val + 1, 99); // Max 99
    } else if (btn.dataset.action === 'minus') {
        val = Math.max(val - 1, 1);  // Min 1
    }
    
    input.value = val;
    
    // Cart update karo
    const productId = btn.closest('[data-product-id]')?.dataset.productId;
    if (productId) {
        updateCartItem(productId, val);
    }
});

// Cart item quantity server par update karo
function updateCartItem(productId, quantity) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Page refresh karke updated totals dikhao
            setTimeout(() => location.reload(), 500);
        }
    });
}

// ============================================
// REMOVE FROM CART
// ============================================
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.cart-remove-btn');
    if (!btn) return;
    
    const productId = btn.dataset.productId;
    const row = btn.closest('.cart-item');
    
    // Animation se row hatao
    row.style.transition = 'all 0.3s ease';
    row.style.opacity = '0';
    row.style.transform = 'translateX(20px)';
    
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=remove&product_id=${productId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => {
                row.remove();
                updateCartBadge(data.cart_count);
                // Agar cart khali ho gaya
                if (data.cart_count === 0) location.reload();
            }, 300);
        }
    });
});

// ============================================
// SEARCH BAR - Live suggestions (simple)
// ============================================
const searchInput = document.querySelector('.search-bar input');
searchInput?.addEventListener('input', debounce(function() {
    // Future mein live search add ki ja sakti hai
}, 300));

// Debounce helper - baar baar fire na ho
function debounce(fn, delay) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

// ============================================
// PRODUCT IMAGE LAZY LOADING
// ============================================
if ('IntersectionObserver' in window) {
    const imgObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imgObserver.unobserve(img);
                }
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => imgObserver.observe(img));
}

// ============================================
// NUMBER COUNTER ANIMATION (Hero stats)
// ============================================
function animateCounter(el, target, duration = 2000) {
    let start = 0;
    const step = target / (duration / 16);
    
    const timer = setInterval(() => {
        start += step;
        if (start >= target) {
            el.textContent = target.toLocaleString() + (el.dataset.suffix || '');
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(start).toLocaleString() + (el.dataset.suffix || '');
        }
    }, 16);
}

// Counters observe karo
const counterEls = document.querySelectorAll('[data-counter]');
if (counterEls.length > 0) {
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                animateCounter(el, parseInt(el.dataset.counter));
                counterObserver.unobserve(el);
            }
        });
    });
    counterEls.forEach(el => counterObserver.observe(el));
}
