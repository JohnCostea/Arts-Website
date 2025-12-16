<?php
/**
 * Get Cart - Session-based
 * Returns cart items from session storage
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => true, 'cart' => []]);
    exit;
}

// Return cart from session (or empty array if no cart)
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

echo json_encode(['success' => true, 'cart' => $cart]);
?>