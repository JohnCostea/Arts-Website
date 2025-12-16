John Costea Art Creations

An elegant e-commerce website for selling original artwork, prints, and merchandise. Built with PHP, MySQL, and vanilla JavaScript, featuring a modern responsive design with dark/light theme support.

✨ Features
🛒 Shopping Experience

Product Catalog - Browse original paintings, art prints, and merchandise
Shopping Cart - Session-based cart with real-time updates
Floating Cart Icon - Quick access cart button with live item count badge
Smooth Checkout - Complete order process with address validation

👤 User Management

User Registration - Secure account creation with validation
User Login - Session-based authentication with rate limiting
Password Security - Bcrypt hashing with strength requirements

⭐ Product Reviews

Customer Reviews - Users can leave ratings and written reviews
Star Ratings - Interactive 5-star rating system
Average Ratings - Displays overall product rating

🎨 Design & UX

Dark/Light Theme - Toggle between themes with cookie persistence
Responsive Design - Works on desktop, tablet, and mobile
Smooth Animations - Subtle transitions and hover effects
Toast Notifications - User feedback for actions

🔒 Security Features

Input Validation - Client-side and server-side validation
SQL Injection Prevention - Prepared statements throughout
XSS Protection - Output escaping and sanitization
CSRF Protection - Form validation
Rate Limiting - Login attempt throttling

🗄️ Database Schema
The application uses MySQL with the following tables:

users - User accounts and authentication
products - Product catalog with categories
categories - Product categories (paintings, prints, merchandise)
orders - Customer orders
order_items - Individual items within orders
user_addresses - Shipping addresses
reviews - Product reviews and ratings

🚀 Installation
Prerequisites

PHP 7.4 or higher
MySQL 5.7 or higher
Apache/Nginx web server

Setup Steps

Copy the repository
git https://github.com/JohnCostea/Arts-Website/


Create the database with setup_database.php
Populate the database with add_products.php

Configure database connection
Edit db_connect.php with your credentials:

php   
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "mydb";
$port = 3306;

🛠️ Technologies Used

PHP - Server-side logic and API endpoints

MySQL - Database management

JavaScript  - Client-side interactivity

CSS - Styling

HTML5 - Semantic markup

Google Fonts - "Lavishly Yours" cursive font
