<?php
/**
 * Registration Page
 * Naya user account banana
 */

require_once 'config/database.php';

// Pehle se login hai?
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form data safely nikalo
    $formData = [
        'name'     => trim($_POST['name'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'phone'    => trim($_POST['phone'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm'  => $_POST['confirm_password'] ?? ''
    ];
    
    // Validation
    if (strlen($formData['name']) < 2) {
        $errors[] = 'Name must be at least 2 characters.';
    }
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($formData['password']) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($formData['password'] !== $formData['confirm']) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (empty($errors)) {
        $db = getDB();
        
        // Email already exists check karo
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([strtolower($formData['email'])]);
        
        if ($check->fetch()) {
            $errors[] = 'This email is already registered. Please login instead.';
        } else {
            // Password hash karo (secure)
            $hashedPassword = password_hash($formData['password'], PASSWORD_DEFAULT);
            
            // User insert karo
            $insert = $db->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $insert->execute([
                $formData['name'],
                strtolower($formData['email']),
                $hashedPassword,
                $formData['phone']
            ]);
            
            $userId = $db->lastInsertId();
            
            // Auto login karo registration ke baad
            $_SESSION['user_id']    = $userId;
            $_SESSION['user_name']  = $formData['name'];
            $_SESSION['user_email'] = $formData['email'];
            $_SESSION['user_role']  = 'customer';
            
            $_SESSION['flash_message'] = 'Account created successfully! Welcome to ' . SITE_NAME . '!';
            $_SESSION['flash_type']    = 'success';
            
            header('Location: index.php');
            exit;
        }
    }
}

$pageTitle = 'Create Account';
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" data-aos="fade-up">
            
            <div class="auth-logo">
                <i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?>
            </div>
            
            <h2 class="auth-title">Create Account</h2>
            <p class="auth-subtitle">Join thousands of happy shoppers today!</p>
            
            <!-- Error messages -->
            <?php if (!empty($errors)): ?>
                <div style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:var(--radius-sm); margin-bottom:20px; font-size:0.9rem;">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix these errors:</strong>
                    <ul style="margin-top:8px; padding-left:16px;">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="name" class="form-control"
                               placeholder="Ahmad Ali"
                               value="<?= htmlspecialchars($formData['name'] ?? '') ?>"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control"
                               placeholder="your@email.com"
                               value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" name="phone" class="form-control"
                               placeholder="+92 300 1234567"
                               value="<?= htmlspecialchars($formData['phone'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="form-control"
                               placeholder="Min 6 characters"
                               required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="confirm_password" class="form-control"
                               placeholder="Repeat your password"
                               required>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
                
            </form>
            
            <div class="auth-switch">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
            
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
