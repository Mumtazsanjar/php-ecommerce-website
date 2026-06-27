<?php
/**
 * Cart AJAX Handler
 * JavaScript se aane wale cart requests handle karta hai
 * Returns: JSON response
 */

require_once __DIR__ . '/../config/database.php';

// Sirf POST requests accept karo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

// Action kya karna hai?
$action     = $_POST['action'] ?? '';
$productId  = (int)($_POST['product_id'] ?? 0);
$quantity   = (int)($_POST['quantity'] ?? 1);

// Cart session initialize karo
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    
    // Cart mein product add karo
    case 'add':
        if ($productId <= 0) {
            jsonResponse(false, 'Invalid product');
        }
        
        // Product exist karta hai check karo
        $db = getDB();
        $stmt = $db->prepare("SELECT id, name, price, sale_price, image_url, stock FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if (!$product) {
            jsonResponse(false, 'Product not found');
        }
        
        // Stock check
        if ($product['stock'] <= 0) {
            jsonResponse(false, 'Product is out of stock');
        }
        
        // Cart mein hai toh quantity badhao, nahi toh naya add karo
        if (isset($_SESSION['cart'][$productId])) {
            $newQty = $_SESSION['cart'][$productId]['quantity'] + $quantity;
            $_SESSION['cart'][$productId]['quantity'] = min($newQty, $product['stock']);
        } else {
            $_SESSION['cart'][$productId] = [
                'product_id' => $product['id'],
                'name'       => $product['name'],
                'price'      => $product['sale_price'] ?: $product['price'],
                'image'      => $product['image_url'],
                'quantity'   => min($quantity, $product['stock'])
            ];
        }
        
        jsonResponse(true, 'Product added to cart', ['cart_count' => getCartCount()]);
        break;
    
    // Quantity update karo
    case 'update':
        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
            jsonResponse(true, 'Cart updated', ['cart_count' => getCartCount()]);
        } else {
            jsonResponse(false, 'Item not in cart');
        }
        break;
    
    // Cart se remove karo
    case 'remove':
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            jsonResponse(true, 'Item removed', ['cart_count' => getCartCount()]);
        } else {
            jsonResponse(false, 'Item not found in cart');
        }
        break;
    
    // Cart ka data return karo
    case 'get':
        $cartTotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }
        jsonResponse(true, 'Cart data', [
            'cart'       => $_SESSION['cart'],
            'cart_count' => getCartCount(),
            'total'      => $cartTotal
        ]);
        break;
    
    default:
        jsonResponse(false, 'Unknown action');
}

/**
 * Cart mein total items count
 */
function getCartCount() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

/**
 * JSON response bhejo aur exit karo
 */
function jsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}
?>
