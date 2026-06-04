<?php
// La La Lush C2C E Commerce system configuration file
// This file handles database connection, sessions, and global settings

// error reporting for development for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details used by all pages in the system

// Database host (localhost is used for XAMPP local development)
define('DB_HOST', 'localhost');

// Name of the database created in phpMyAdmin
define('DB_NAME', 'lalalush_db');

// MySQL username (default is root for XAMPP)
define('DB_USER', 'root');

// MySQL password (empty by default on XAMPP)
define('DB_PASS', '');

// Website base URL used for links and redirects
define('SITE_URL', 'http://localhost/lalalush');

// Website name used in page titles and system messages
define('SITE_NAME', 'La La Lush');

// Create connection between PHP and MySQL database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if connection failed and stop execution if it does
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set database character encoding for emojis
mysqli_set_charset($conn, "utf8");

// Start session to track logged-in users across pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>