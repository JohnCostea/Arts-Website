<?php
/**
 * Add Review for a Product
 * Allows logged-in users to submit product reviews with ratings
 * Includes validation and prevents duplicate reviews
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
        'message' => 'Please log in to submit a review'
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate input
$validator = new Validator($_POST);

$validator
    ->validate('product_id', 'Product', 'required|integer')
    ->validate('rating', 'Rating', 'required|integer|in:1,2,3,4,5')
    ->validate('comment', 'Review', 'required|min:10|max:1000');

if ($validator->fails()) {
    echo json_encode([
        'success' => false,
        'message' => $validator->firstError(),
        'errors' => $validator->errors()
    ]);
    exit;
}

// Get validated data
$product_id = intval($_POST['product_id']);
$rating = intval($_POST['rating']);
$comment = trim($_POST['comment']);

// Verify product exists
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    echo json_encode([
        'success' => false,
        'message' => 'Product not found'
    ]);
    exit;
}
$stmt->close();

// Check if user already reviewed this product
$stmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    echo json_encode([
        'success' => false,
        'message' => 'You have already reviewed this product'
    ]);
    exit;
}
$stmt->close();

// Insert review
$stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");

if (!$stmt) {
    error_log("Failed to prepare review insert: " . $conn->error);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit review'
    ]);
    exit;
}

$stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);

if ($stmt->execute()) {
    $review_id = $conn->insert_id;
    
    // Log successful review
    error_log("Review submitted: User $user_id, Product $product_id, Rating $rating");
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Review submitted successfully!',
        'review_id' => $review_id
    ]);
} else {
    error_log("Failed to insert review: " . $stmt->error);
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit review. Please try again.'
    ]);
}
?>