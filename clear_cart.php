<?php
/**
 * Removes all items from session cart
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in']);
    exit;
}

// Clear cart from session
$_SESSION['cart'] = [];

echo json_encode(['success' => true, 'message' => 'Cart cleared']);

?>
