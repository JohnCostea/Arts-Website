<?php
/**
 * User Registration with Validation
 */

header('Content-Type: application/json');

// Include database connection
include "db_connect.php";

// Include validation helper
require_once "Validator.php";

// Validate input
$validator = new Validator($_POST);

$validator
    ->validate('name', 'Name', 'required|alpha|min:2|max:100')
    ->validate('email', 'Email', 'required|email|max:255|unique:users,email')
    ->validate('password', 'Password', 'required|password|min:8');

// Check if validation failed
if ($validator->fails()) {
    echo json_encode([
        'success' => false, 
        'message' => $validator->firstError(),
        'errors' => $validator->errors()
    ]);
    exit;
}

// Get validated and sanitized data
$validated = $validator->validated();
$name = $validated['name'];
$email = $validator->sanitizeEmail($validated['email']);
$password = $_POST['password']; // Don't sanitize password - hash it instead

// Additional security check: verify email is truly valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid email address'
    ]);
    exit;
}

// Hash password with bcrypt
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check password hash succeeded
if ($hashedPassword === false) {
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed. Please try again.'
    ]);
    exit;
}

// Prepare statement to insert new user
$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed. Please try again.'
    ]);
    exit;
}

$stmt->bind_param("sss", $name, $email, $hashedPassword);

// Execute insert
if ($stmt->execute()) {
    // Success - log for monitoring
    error_log("New user registered: " . $email);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Registration successful! You can now login.'
    ]);
} else {
    // Log error but don't expose database details to user
    error_log("Registration error: " . $stmt->error);
    
    // Check for duplicate email (in case of race condition)
    if ($conn->errno === 1062) {
        echo json_encode([
            'success' => false, 
            'message' => 'Email already registered'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Registration failed. Please try again.'
        ]);
    }
}

$stmt->close();
$conn->close();
exit;

?>
