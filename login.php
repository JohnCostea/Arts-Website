<?php
/**
 * User Login with Validation
 * Handles user authentication with comprehensive validation and security measures
 */

session_start();
header('Content-Type: application/json');

// Include database connection
include "db_connect.php";

// Include validation helper
require_once "Validator.php";

// Rate limiting check (simple implementation)
// In production, use Redis or database-based rate limiting
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_last_attempt'] = time();
}

// Reset attempts after 15 minutes
if (time() - $_SESSION['login_last_attempt'] > 900) {
    $_SESSION['login_attempts'] = 0;
}

// Block after 5 failed attempts
if ($_SESSION['login_attempts'] >= 5) {
    echo json_encode([
        'success' => false, 
        'message' => 'Too many login attempts. Please try again in 15 minutes.'
    ]);
    exit;
}

// Validate input
$validator = new Validator($_POST);

$validator
    ->validate('email', 'Email', 'required|email|max:255')
    ->validate('password', 'Password', 'required|min:8|max:255');

// Check if validation failed
if ($validator->fails()) {
    $_SESSION['login_attempts']++;
    $_SESSION['login_last_attempt'] = time();
    
    echo json_encode([
        'success' => false, 
        'message' => $validator->firstError(),
        'errors' => $validator->errors()
    ]);
    exit;
}

// Get validated and sanitized email
$email = $validator->sanitizeEmail($_POST['email']);
$password = $_POST['password']; // Don't sanitize password

// Additional security check
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_attempts']++;
    $_SESSION['login_last_attempt'] = time();
    
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Prepare statement to fetch user
$stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode([
        'success' => false, 
        'message' => 'Login failed. Please try again.'
    ]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Login successful
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Reset login attempts
        $_SESSION['login_attempts'] = 0;
        
        // Set user session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['login_time'] = time();
        
        // Log successful login
        error_log("Successful login: " . $email);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'username' => $user['name']
        ]);
    } else {
        // Invalid password
        $_SESSION['login_attempts']++;
        $_SESSION['login_last_attempt'] = time();
        
        // Log failed attempt
        error_log("Failed login attempt for: " . $email . " (invalid password)");
        
        // Generic error message to prevent user enumeration
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid email or password'
        ]);
    }
} else {
    // User not found
    $_SESSION['login_attempts']++;
    $_SESSION['login_last_attempt'] = time();
    
    // Log failed attempt
    error_log("Failed login attempt for: " . $email . " (user not found)");
    
    // Generic error message to prevent user enumeration
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid email or password'
    ]);
}

$stmt->close();
$conn->close();
exit;
?>