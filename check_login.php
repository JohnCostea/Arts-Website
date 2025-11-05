<?php
/**
 * checks if a user is currently logged in
 * Used by frontend JavaScript to display appropriate UI
 * Returns user login status and username if logged in
 */

// Start PHP session to access session variables
session_start();

// Set response content type to JSON
header('Content-Type: application/json');

// Check if user session variables exist (user is logged in)
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // User is logged in - return user data
    echo json_encode([
        'loggedIn' => true,
        'username' => $_SESSION['username']
    ]);
} else {
    // User is not logged in
    echo json_encode([
        'loggedIn' => false
    ]);
}
?>