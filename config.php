<?php
// La La Lush C2C E Commerce system configuration file
// This file handles database connection, sessions, and global settings

// Turn off error display on live server
error_reporting(0);
ini_set('display_errors', 0);

// Database host - localhost on shared hosting
define('DB_HOST', 'localhost');

// Live database name prefixed by hosting provider
define('DB_NAME', 'lalalush_lalalushdb');

// Live database username
define('DB_USER', 'lalalush_user1');

// Live database password
define('DB_PASS', 'Jellyf1$h23');

// Website base URL
define('SITE_URL', 'https://www.lalalush.site');

// Website name used in page titles and system messages
define('SITE_NAME', 'La La Lush');

// Create connection between PHP and MySQL database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if connection failed and stop execution if it does
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set database character encoding for emojis and special characters
mysqli_set_charset($conn, "utf8");

// Start session to track logged-in users across pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>