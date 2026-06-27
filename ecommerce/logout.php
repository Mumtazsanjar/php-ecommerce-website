<?php
/**
 * Logout - Session destroy karke login page par bhejo
 */
require_once 'config/database.php';

// Session variables clear karo
$_SESSION = [];

// Session destroy karo
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Login page par redirect karo
header('Location: login.php');
exit;
?>
