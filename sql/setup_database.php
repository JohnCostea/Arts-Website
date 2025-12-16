<?php
/**
 * Database Setup Script for John Costea Art Creations
 * Session-based Cart Version (No cart table)
 * 
 * This script creates all necessary tables for the e-commerce website
 * Cart is stored in sessions, not database
 * 
 * Run this file once to set up the database structure
 * WARNING: This will drop existing tables and recreate them
 */

// Database connection parameters
$servername = "ionutproject";
$username = "root";
$password = "";
$dbname = "mydb";
$port = 3306;

// Create connection to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Force InnoDB as default storage engine for this session
$conn->query("SET default_storage_engine=InnoDB");

echo "Connected to database successfully<br><br>";

// Drop old tables (optional cleanup for re-run safety)
echo "Dropping old tables if they exist...<br>";
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("DROP TABLE IF EXISTS reviews, order_items, orders, user_addresses, products, categories, users");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");
echo "Old tables dropped<br><br>";

// ==========================================================
// USERS TABLE
// ==========================================================
echo "Creating USERS table...<br>";
$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ USERS table created successfully<br>";
} else {
    echo "❌ Error creating USERS table: " . $conn->error . "<br>";
}

// ==========================================================
// CATEGORIES TABLE
// ==========================================================
echo "Creating CATEGORIES table...<br>";
$sql = "CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ CATEGORIES table created successfully<br>";
} else {
    echo "❌ Error creating CATEGORIES table: " . $conn->error . "<br>";
}

// Insert initial categories
echo "Inserting categories...<br>";
if ($conn->query("INSERT INTO categories (name) VALUES ('painting'), ('print'), ('merchandise')") === TRUE) {
    echo "✅ Categories inserted successfully<br>";
} else {
    echo "❌ Error inserting categories: " . $conn->error . "<br>";
}

// ==========================================================
// PRODUCTS TABLE
// ==========================================================
echo "Creating PRODUCTS table...<br>";
$sql = "CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ PRODUCTS table created successfully<br>";
} else {
    echo "❌ Error creating PRODUCTS table: " . $conn->error . "<br>";
}

// ==========================================================
// ORDERS TABLE
// ==========================================================
echo "Creating ORDERS table...<br>";
$sql = "CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status VARCHAR(50) DEFAULT 'unpaid',
    payment_method VARCHAR(50) DEFAULT 'card',
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ ORDERS table created successfully<br>";
} else {
    echo "❌ Error creating ORDERS table: " . $conn->error . "<br>";
}

// ==========================================================
// ORDER_ITEMS TABLE
// ==========================================================
echo "Creating ORDER_ITEMS table...<br>";
$sql = "CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ ORDER_ITEMS table created successfully<br>";
} else {
    echo "❌ Error creating ORDER_ITEMS table: " . $conn->error . "<br>";
}

// ==========================================================
// USER_ADDRESSES TABLE
// ==========================================================
echo "Creating USER_ADDRESSES table...<br>";
$sql = "CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Ireland',
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ USER_ADDRESSES table created successfully<br>";
} else {
    echo "❌ Error creating USER_ADDRESSES table: " . $conn->error . "<br>";
}

// ==========================================================
// REVIEWS TABLE
// ==========================================================
echo "Creating REVIEWS table...<br>";
$sql = "CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
if ($conn->query($sql) === TRUE) {
    echo "✅ REVIEWS table created successfully<br>";
} else {
    echo "❌ Error creating REVIEWS table: " . $conn->error . "<br>";
}




// Display final summary
echo "<br>";
echo "=================================================<br>";
echo "✅ DATABASE SETUP COMPLETE!<br>";
echo "=================================================<br>";
echo "Total tables created: 7 <br>";
echo "<br>";
echo "Tables created:<br>";
echo "1. users - Customer accounts<br>";
echo "2. categories - Product categories<br>";
echo "3. products - Art items for sale<br>";
echo "4. orders - Customer orders<br>";
echo "5. order_items - Individual items in orders<br>";
echo "6. user_addresses - Shipping addresses<br>";
echo "7. reviews - Product ratings and comments<br>";
echo "Database is ready!<br>";
echo "- Register new user accounts<br>";
echo "- Browse products by category<br>";
echo "- Add items to cart (stored in session)<br>";
echo "- Place orders with shipping information<br>";
echo "<br>";

$conn->close();
?>