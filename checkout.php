<?php
/**
 * Checkout with Validation and Price Verification
 */

session_start();
header('Content-Type: application/json');

// Include database connection and validator
include "db_connect.php";
require_once "Validator.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please log in to checkout'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get cart from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Cart is empty'
    ]);
    exit;
}

// Get request data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['address'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request data'
    ]);
    exit;
}

// Validate address data
$addressData = $data['address'];
$validator = new Validator($addressData);

$validator
    ->validate('address_line1', 'Address Line 1', 'required|max:255')
    ->validate('address_line2', 'Address Line 2', 'max:255')
    ->validate('city', 'City', 'required|alpha|max:100')
    ->validate('state', 'State/County', 'required|alpha|max:100')
    ->validate('postal_code', 'Postal Code', 'required|postalcode|max:20')
    ->validate('country', 'Country', 'required|alpha|max:100');

if ($validator->fails()) {
    echo json_encode([
        'success' => false, 
        'message' => $validator->firstError(),
        'errors' => $validator->errors()
    ]);
    exit;
}

// Validate payment method
$payment_method = isset($data['payment_method']) ? $data['payment_method'] : '';
$validPaymentMethods = ['card', 'paypal', 'bank_transfer', 'other'];

if (!in_array($payment_method, $validPaymentMethods)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid payment method'
    ]);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // CRITICAL SECURITY: Verify prices from database, not from session
    // This prevents price manipulation attacks
    $total_amount = 0;
    $verified_cart = [];

    foreach ($cart as $item) {
        // Validate cart item structure
        if (!isset($item['id']) || !isset($item['quantity'])) {
            throw new Exception("Invalid cart item");
        }

        // Get current price from database
        $stmt = $conn->prepare("SELECT id, name, price, image_url FROM products WHERE id = ?");
        $stmt->bind_param("i", $item['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            throw new Exception("Product not found in cart");
        }

        $product = $result->fetch_assoc();
        $stmt->close();

        // Validate quantity
        $quantity = intval($item['quantity']);
        if ($quantity <= 0 || $quantity > 100) {
            throw new Exception("Invalid quantity");
        }

        // Calculate with VERIFIED price from database
        $verified_price = floatval($product['price']);
        $item_total = $verified_price * $quantity;
        $total_amount += $item_total;

        // Store verified cart item
        $verified_cart[] = [
            'id' => intval($product['id']),
            'name' => $product['name'],
            'price' => $verified_price,
            'quantity' => $quantity,
            'imageUrl' => $product['image_url']
        ];
    }

    // Validate total amount is reasonable
    if ($total_amount <= 0 || $total_amount > 1000000) {
        throw new Exception("Invalid order total");
    }

    // Create order
    $payment_status = 'unpaid';
    $status = 'pending';

    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, total_amount, payment_status, payment_method, status) 
         VALUES (?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        throw new Exception("Failed to prepare order statement");
    }

    $stmt->bind_param("idsss", $user_id, $total_amount, $payment_status, $payment_method, $status);
    
    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception("Failed to create order");
    }

    $order_id = $conn->insert_id;
    $stmt->close();

    // Add order items with verified prices
    $stmt = $conn->prepare(
        "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) 
         VALUES (?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        throw new Exception("Failed to prepare order items statement");
    }

    foreach ($verified_cart as $item) {
        $stmt->bind_param(
            "iisid", 
            $order_id, 
            $item['id'], 
            $item['name'], 
            $item['quantity'], 
            $item['price']
        );
        
        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception("Failed to add order item");
        }
    }
    $stmt->close();

    // Save validated address
    $validatedAddress = $validator->validated();
    
    $stmt = $conn->prepare(
        "INSERT INTO user_addresses 
         (user_id, address_line1, address_line2, city, state, postal_code, country) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        throw new Exception("Failed to prepare address statement");
    }

    $addr_line1 = $validatedAddress['address_line1'];
    $addr_line2 = $validatedAddress['address_line2'] ?? '';
    $city = $validatedAddress['city'];
    $state = $validatedAddress['state'];
    $postal_code = $validatedAddress['postal_code'];
    $country = $validatedAddress['country'];

    $stmt->bind_param(
        "issssss", 
        $user_id, 
        $addr_line1, 
        $addr_line2, 
        $city, 
        $state, 
        $postal_code, 
        $country
    );
    
    if (!$stmt->execute()) {
        $stmt->close();
        throw new Exception("Failed to save address");
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();

    // Clear cart from session after successful order
    $_SESSION['cart'] = [];

    // Log successful order
    error_log("Order created successfully: Order ID $order_id, User ID $user_id, Total: $total_amount");

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'total' => number_format($total_amount, 2)
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Log error
    error_log("Checkout error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Order failed. Please try again.'
    ]);
}

$conn->close();
exit;

?>
