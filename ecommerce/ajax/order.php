<?php
/**
 * Order AJAX Handler
 * "Order Now" button se direct order place hota hai
 * JSON response return karta hai
 */

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Sirf POST accept karo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$action = $_POST['action'] ?? '';

// ============================================
// ORDER PLACE KARO
// ============================================
if ($action === 'place_order') {
    $db = getDB();

    // Form data nikalo aur sanitize karo
    $productId   = (int)($_POST['product_id'] ?? 0);
    $quantity    = max(1, (int)($_POST['quantity'] ?? 1));
    $custName    = trim($_POST['customer_name'] ?? '');
    $custPhone   = trim($_POST['customer_phone'] ?? '');
    $custAddress = trim($_POST['customer_address'] ?? '');
    $custCity    = trim($_POST['customer_city'] ?? '');
    $payment     = $_POST['payment_method'] ?? 'cod';
    $notes       = trim($_POST['notes'] ?? '');

    // ---- Validation ----
    $errors = [];
    if ($productId <= 0)        $errors[] = 'Invalid product.';
    if (strlen($custName) < 2)  $errors[] = 'Please enter your full name.';
    if (strlen($custPhone) < 7) $errors[] = 'Please enter a valid phone number.';
    if (empty($custAddress))    $errors[] = 'Please enter your delivery address.';
    if (empty($custCity))       $errors[] = 'Please select your city.';

    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }

    // ---- Product check ----
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    if ($product['stock'] < $quantity) {
        echo json_encode([
            'success' => false,
            'message' => "Only {$product['stock']} items in stock."
        ]);
        exit;
    }

    // ---- Price calculate karo ----
    $unitPrice   = $product['sale_price'] ?: $product['price'];
    $subtotal    = $unitPrice * $quantity;
    $shipping    = $subtotal >= 2000 ? 0 : 200;
    $total       = $subtotal + $shipping;

    // ---- Unique order number banao ----
    $orderNumber = 'SZ-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    $fullAddress = "$custAddress, $custCity";

    try {
        $db->beginTransaction();

        // ---- Orders table mein insert ----
        $orderStmt = $db->prepare("
            INSERT INTO orders 
                (user_id, customer_name, customer_phone, customer_city, 
                 order_number, total_amount, status, payment_method, shipping_address, notes)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?)
        ");
        $orderStmt->execute([
            $_SESSION['user_id'] ?? null,
            $custName,
            $custPhone,
            $custCity,
            $orderNumber,
            $total,
            $payment,
            $fullAddress,
            $notes
        ]);
        $orderId = $db->lastInsertId();

        // ---- Order items insert ----
        $itemStmt = $db->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $itemStmt->execute([$orderId, $productId, $quantity, $unitPrice]);

        // ---- Stock kam karo ----
        $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")
           ->execute([$quantity, $productId]);

        $db->commit();

        // Success response
        echo json_encode([
            'success'      => true,
            'message'      => 'Order placed successfully!',
            'order_number' => $orderNumber,
            'order_id'     => $orderId,
            'product_id'   => $productId,
            'product_name' => $product['name'],
            'product_img'  => $product['image_url'],
            'total'        => $total,
            'customer_name'=> $custName
        ]);

    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Order failed. Please try again.']);
    }
    exit;
}

// ============================================
// PRODUCT REVIEW/RATING SUBMIT KARO
// ============================================
if ($action === 'submit_review') {
    $db = getDB();

    $productId    = (int)($_POST['product_id'] ?? 0);
    $orderId      = (int)($_POST['order_id'] ?? 0);
    $rating       = (int)($_POST['rating'] ?? 0);
    $reviewerName = trim($_POST['reviewer_name'] ?? '');
    $reviewText   = trim($_POST['review_text'] ?? '');

    // Validation
    if ($productId <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Please select a star rating.']);
        exit;
    }
    if (empty($reviewerName)) {
        echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
        exit;
    }

    try {
        // Review insert karo (duplicate hoga toh ignore karo)
        $stmt = $db->prepare("
            INSERT IGNORE INTO product_reviews 
                (product_id, order_id, user_id, reviewer_name, rating, review_text)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $productId,
            $orderId ?: null,
            $_SESSION['user_id'] ?? null,
            $reviewerName,
            $rating,
            $reviewText
        ]);

        if ($db->lastInsertId()) {
            // Product ki average rating update karo
            $db->prepare("
                UPDATE products SET 
                    rating = (
                        SELECT ROUND(AVG(rating), 1) 
                        FROM product_reviews 
                        WHERE product_id = ?
                    ),
                    reviews_count = (
                        SELECT COUNT(*) 
                        FROM product_reviews 
                        WHERE product_id = ?
                    )
                WHERE id = ?
            ")->execute([$productId, $productId, $productId]);

            echo json_encode(['success' => true, 'message' => 'Thank you for your review!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'You have already reviewed this product.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Could not save review.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);
exit;
?>
