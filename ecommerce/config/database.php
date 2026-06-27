<?php
/**
 * Database Configuration File
 * Ye file database connection handle karti hai
 * XAMPP default settings use ki gayi hain
 */

// Database connection settings
define('DB_HOST', 'localhost');      // XAMPP ka default host
define('DB_USER', 'root');           // XAMPP ka default username
define('DB_PASS', '');               // XAMPP mein password khali hota hai
define('DB_NAME', 'ecommerce_db');   // Hamara database name

// Site configuration
define('SITE_NAME', 'ShopZone');
define('SITE_URL', 'http://localhost/E-Commerse%20web/ecommerce');
define('CURRENCY', 'Rs.');

/**
 * Database connection function
 * PDO use karta hai secure queries ke liye
 */
function getDB() {
    static $pdo = null; // Static variable - ek baar connect karo, baar baar use karo
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Errors throw karo
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Array format mein data do
                PDO::ATTR_EMULATE_PREPARES   => false,                    // Real prepared statements
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Production mein yeh message mat dikhao - log file mein likhna chahiye
            die('<div style="background:#ff4444;color:white;padding:20px;text-align:center;font-family:sans-serif;">
                <h2>Database Connection Failed</h2>
                <p>Kripya XAMPP start karein aur database import karein.</p>
                <small>' . $e->getMessage() . '</small>
            </div>');
        }
    }
    
    return $pdo;
}

// Session start karo agar pehle se nahi hua
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
