<?php
/**
 * Add to Cart - Session-based
 * Stores cart in PHP session
 */

session_start();
header('Content-Type: application/json');
include "db_connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart']);
    exit;
}

// Get product ID
$product_id = $_POST['product_id'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

// Verify product exists and get details
$stmt = $conn->prepare("SELECT id, name, price, image_url, description FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$product = $result->fetch_assoc();

// Initialize cart in session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $product_id) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}

// If not in cart, add new item
if (!$found) {
    $_SESSION['cart'][] = [
        'id' => intval($product['id']),
        'name' => $product['name'],
        'price' => floatval($product['price']),
        'quantity' => intval($quantity),
        'imageUrl' => $product['image_url'],
        'description' => $product['description']
    ];
}

echo json_encode(['success' => true, 'message' => 'Added to cart']);

$stmt->close();
$conn->close();

?>
