<?php
/**
 * Login Page
 * User authentication - email aur password se login
 */

require_once 'config/database.php';

// Agar pehle se login hai toh homepage par bhejo
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// Form submit hua?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([strtolower($email)]);
        $user = $stmt->fetch();
        
        // Password verify karo
        if ($user && password_verify($password, $user['password'])) {
            // Login successful - session mein save karo
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email']= $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            $_SESSION['flash_message'] = 'Welcome back, ' . $user['name'] . '!';
            $_SESSION['flash_type']    = 'success';
            
            // Admin ko admin panel bhejo, baaki ko homepage
            header('Location: ' . ($user['role'] === 'admin' ? 'admin/index.php' : 'index.php'));
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}

$pageTitle = 'Login';
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="container">
        <div class="auth-card" data-aos="fade-up">
            
            <!-- Logo -->
            <div class="auth-logo">
                <i class="fas fa-shopping-bag"></i> <?= SITE_NAME ?>
            </div>
            
            <h2 class="auth-title">Welcome Back!</h2>
            <p class="auth-subtitle">Sign in to your account to continue</p>
            
            <!-- Error message -->
            <?php if ($error): ?>
                <div style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; padding:12px 16px; border-radius:var(--radius-sm); margin-bottom:20px; font-size:0.9rem; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="">
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control" 
                               placeholder="your@email.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               required autocomplete="email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Enter your password"
                               required autocomplete="current-password">
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <!-- Demo credentials -->
            <div style="background:var(--gray-100); border-radius:var(--radius-sm); padding:14px; margin-top:20px; font-size:0.82rem; color:var(--gray-600);">
                <strong style="color:var(--dark);">Demo Credentials:</strong><br>
                Admin: admin@shop.com / <em>password: admin123</em>
            </div>
            
            <div class="auth-switch">
                Don't have an account? <a href="register.php">Create one now</a>
            </div>
            
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
