<?php
// Load the main configuration file which also starts the session
require_once 'config.php';

// This file handles logging the user out of the system

// Remove all session data stored for the current user
$_SESSION = array();

// Completely destroy the session on the server
// This ensures the user is fully logged out and cannot reuse session data
session_destroy();

// Redirect the user back to the login page after logout
// This improves user flow and prevents access to protected pages
header('Location: login.php');
exit();
?>