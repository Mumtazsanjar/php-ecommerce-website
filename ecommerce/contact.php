<?php
/**
 * Contact Page
 */

require_once 'config/database.php';
$pageTitle = 'Contact Us';

$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Normally email bheja jata hai - yahan just success dikhate hain
    $messageSent = true;
}

require_once 'includes/header.php';
?>

<div class="page-title-bar">
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Contact Us</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Contact</span>
        </div>
    </div>
</div>

<div style="padding:60px 0 80px;">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:start;">
            
            <!-- Contact Info -->
            <div data-aos="fade-right">
                <div class="section-tag" style="margin-bottom:16px;"><i class="fas fa-comments"></i> Get In Touch</div>
                <h2 class="section-title" style="text-align:left;">We'd Love to <span>Hear From You</span></h2>
                <p style="color:var(--gray-600); margin:16px 0 36px; line-height:1.8;">
                    Have a question about your order, need help with a product, or just want to say hello? 
                    Our friendly team is here to help you!
                </p>
                
                <!-- Contact Details -->
                <div style="display:flex; flex-direction:column; gap:20px; margin-bottom:40px;">
                    <?php
                    $contacts = [
                        ['fas fa-map-marker-alt', '#6366f1', '123 Main Street', 'Karachi, Pakistan'],
                        ['fas fa-phone',          '#ec4899', '+92 300 1234567', 'Mon-Sat, 9am - 6pm'],
                        ['fas fa-envelope',       '#f59e0b', 'support@shopzone.pk', 'Reply within 24 hours'],
                        ['fas fa-clock',          '#10b981', 'Business Hours', 'Mon-Sat: 9am - 6pm PKT'],
                    ];
                    foreach ($contacts as [$icon, $color, $title, $sub]):
                    ?>
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div style="width:52px; height:52px; background:<?= $color ?>22; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:<?= $color ?>; flex-shrink:0;">
                                <i class="<?= $icon ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight:700; color:var(--dark);"><?= $title ?></div>
                                <div style="font-size:0.85rem; color:var(--gray-600);"><?= $sub ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Social Links -->
                <div>
                    <p style="font-weight:600; margin-bottom:14px; color:var(--dark);">Follow us on social media:</p>
                    <div class="social-links">
                        <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-btn youtube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div data-aos="fade-left">
                <div style="background:white; border-radius:var(--radius); padding:40px; box-shadow:var(--shadow-lg);">
                    
                    <?php if ($messageSent): ?>
                        <div style="text-align:center; padding:20px;">
                            <div style="width:72px; height:72px; background:rgba(16,185,129,0.15); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:2rem; color:var(--success);">
                                <i class="fas fa-check"></i>
                            </div>
                            <h3 style="font-size:1.4rem; font-weight:800; color:var(--dark); margin-bottom:10px;">Message Sent!</h3>
                            <p style="color:var(--gray-600);">Thank you for contacting us. We'll get back to you within 24 hours.</p>
                        </div>
                    <?php else: ?>
                        <h3 style="font-size:1.3rem; font-weight:800; color:var(--dark); margin-bottom:24px;">Send us a Message</h3>
                        <form method="POST" style="display:flex; flex-direction:column; gap:18px;">
                            
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                                <div class="form-group">
                                    <label class="form-label">Your Name</label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="name" class="form-control" placeholder="Ahmad Ali" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Subject</label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-tag input-icon"></i>
                                    <select name="subject" class="form-control" style="padding-left:42px;">
                                        <option>Order Issue</option>
                                        <option>Product Inquiry</option>
                                        <option>Return Request</option>
                                        <option>Payment Issue</option>
                                        <option>General Question</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="5" 
                                          placeholder="Tell us how we can help you..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn-submit" style="border-radius:var(--radius-sm);">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                            
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
