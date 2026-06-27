<?php
/**
 * Search Results Page
 * Search query ko products.php par redirect karta hai with query param
 */

// Search page actually products.php ko use karta hai
$q = trim($_GET['q'] ?? '');
header('Location: products.php?q=' . urlencode($q));
exit;
?>
