
<?php
/**
 * Retrieves products from the database
 * Can fetch all products or filter by category_id
 * Returns JSON array of products with all details
 * 
 * - get_products.php (returns all products)
 * - get_products.php?category_id=1 (returns only paintings)
 */

// Set response content type to JSON
header('Content-Type: application/json');

// Include database connection
include "db_connect.php";

// Get optional category_id parameter from URL query string
$category_id = $_GET['category_id'] ?? null;

// Build SQL query based on whether category filter is provided
if ($category_id) {
    // Query for specific category
    $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.category_id, c.name as category, p.image_url
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            WHERE p.category_id = ?");
    $stmt->bind_param("i", $category_id);  // Bind category_id as integer
} else {
    // Query for all products (no filter)
    $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.category_id, c.name as category, p.image_url
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id");
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// Initialize empty array to store products
$products = [];

// Fetch all products and add to array
while ($row = $result->fetch_assoc()) {
    // Convert numeric fields to appropriate types
    $products[] = [
        'id' => intval($row['id']),              // Product ID as integer
        'name' => $row['name'],                  // Product name
        'description' => $row['description'],    // Product description
        'price' => $row['price'],                // Price as decimal string
        'category' => $row['category'],          // Category name (painting/print/merchandise)
        'category_id' => intval($row['category_id']),  // Category ID as integer
        'image_url' => $row['image_url']         // Product image URL
    ];
}

// Return products as JSON array
echo json_encode($products);

// Clean up
$stmt->close();
$conn->close();

?>
