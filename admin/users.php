<?php
// This file is the Admin Users Management page for La La Lush
// It allows admins to view all users, change roles, and delete accounts
// This is part of Role-Based Access Control (RBAC)

require_once dirname(__DIR__) . '/config.php';

// Ensure session is active before accessing session variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {

    $target_uid = (int)$_POST['target_uid'];
    $new_role = mysqli_real_escape_string($conn, $_POST['new_role']);

    // Prevent admin changing own role
    if ($target_uid !== (int)$_SESSION['user_id']) {
        mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE user_id=$target_uid");
    }

    header('Location: users.php?updated=1');
    exit();
}

// Handle delete user
if (isset($_GET['delete'])) {

    $del_uid = (int)$_GET['delete'];

    // Prevent self delete
    if ($del_uid !== (int)$_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE user_id=$del_uid");
    }

    header('Location: users.php?deleted=1');
    exit();
}

// Get all users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>