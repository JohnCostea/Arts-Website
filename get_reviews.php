<?php
/**
 * Get Reviews for a Product
 * Fetches all reviews for a specific product with user information
 * Returns JSON array of reviews with ratings
 */

header('Content-Type: application/json');

// Include database connection
include "db_connect.php";

// Get product_id from query parameter
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID'
    ]);
    exit;
}

// Prepare query to get reviews with user information
$stmt = $conn->prepare("
    SELECT 
        r.id,
        r.rating,
        r.comment,
        r.created_at,
        u.name as user_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");

if (!$stmt) {
    error_log("Failed to prepare statement: " . $conn->error);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch reviews'
    ]);
    exit;
}

$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
$total_rating = 0;
$review_count = 0;

while ($row = $result->fetch_assoc()) {
    $reviews[] = [
        'id' => intval($row['id']),
        'rating' => intval($row['rating']),
        'comment' => htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8'),
        'user_name' => htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8'),
        'created_at' => $row['created_at'],
        'formatted_date' => date('M d, Y', strtotime($row['created_at']))
    ];
    
    $total_rating += intval($row['rating']);
    $review_count++;
}

// Calculate average rating
$average_rating = $review_count > 0 ? round($total_rating / $review_count, 1) : 0;

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'reviews' => $reviews,
    'average_rating' => $average_rating,
    'review_count' => $review_count
]);
?>