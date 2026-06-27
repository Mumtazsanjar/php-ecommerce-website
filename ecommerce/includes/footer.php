<?php
/**
 * Footer File - Har page ka neeche wala hissa
 * Links, newsletter, social media sab yahan
 */
?>
</main> <!-- main-content end -->

<!-- ============================================
     FOOTER SECTION
     ============================================ -->
<footer class="site-footer">
    
    <!-- Footer Top - Links aur info -->
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                
                <!-- Brand Column -->
                <div class="footer-col">
                    <a href="<?= SITE_URL ?>/index.php" class="footer-logo">
                        <i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?>
                    </a>
                    <p class="footer-desc">
                        Pakistan ka number one online shopping destination. 
                        Quality products, best prices, fast delivery.
                    </p>
                    <!-- Social Media Links -->
                    <div class="social-links">
                        <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-btn youtube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-col">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?= SITE_URL ?>/index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="<?= SITE_URL ?>/products.php"><i class="fas fa-chevron-right"></i> All Products</a></li>
                        <li><a href="<?= SITE_URL ?>/products.php?sale=1"><i class="fas fa-chevron-right"></i> Sale Items</a></li>
                        <li><a href="<?= SITE_URL ?>/cart.php"><i class="fas fa-chevron-right"></i> Shopping Cart</a></li>
                        <li><a href="<?= SITE_URL ?>/contact.php"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div class="footer-col">
                    <h4 class="footer-title">Customer Service</h4>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Shipping Policy</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Return Policy</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Privacy Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/orders.php"><i class="fas fa-chevron-right"></i> Track Order</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div class="footer-col">
                    <h4 class="footer-title">Contact Us</h4>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Main Street, Karachi, Pakistan</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>+92 300 1234567</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>support@shopzone.pk</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon-Sat: 9am - 6pm</span>
                        </li>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom - Copyright -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Made with <i class="fas fa-heart" style="color:#ef4444"></i></p>
                <div class="payment-icons">
                    <span>We Accept:</span>
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fas fa-money-bill-wave" title="Cash on Delivery"></i>
                    <i class="fas fa-mobile-alt" title="JazzCash / Easypaisa"></i>
                </div>
            </div>
        </div>
    </div>
    
</footer>

<!-- Order Now + Rating Modals - sab pages par available -->
<?php require_once __DIR__ . '/order_modal.php'; ?>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" title="Back to top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- ============================================
     JAVASCRIPT FILES
     ============================================ -->
<!-- AOS Animation library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<!-- Main JS file -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>

<script>
    // AOS (Animate On Scroll) initialize karo
    AOS.init({
        duration: 700,    // Animation duration milliseconds mein
        once: true,       // Sirf ek baar animate karo
        offset: 80        // Kitni door se animation shuru ho
    });
</script>

</body>
</html>
