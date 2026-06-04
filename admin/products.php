<?php
// Admin Products Management page
// Allows admin to manage all products (CRUD controls)

require_once dirname(__DIR__) . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Toggle product visibility (active/inactive)
if (isset($_GET['toggle'])) {

    $pid = (int)$_GET['toggle'];

    mysqli_query($conn, "
        UPDATE products 
        SET is_active = NOT is_active 
        WHERE product_id = $pid
    ");

    header('Location: products.php');
    exit();
}

// Delete product
if (isset($_GET['delete'])) {

    $pid = (int)$_GET['delete'];

    mysqli_query($conn, "DELETE FROM products WHERE product_id = $pid");

    header('Location: products.php?deleted=1');
    exit();
}

// Fetch products (IMPORTANT FIX: include stock + active filter visibility display)
$products = mysqli_query($conn, "
    SELECT p.*, c.category_name, u.username AS seller
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    JOIN users u ON p.seller_id = u.user_id
    ORDER BY p.created_at DESC
");
?>