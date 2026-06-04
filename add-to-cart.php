<?php
session_start();
require_once 'config.php';

// Validate product id
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// check if already in cart
$check = mysqli_query($conn, "
    SELECT * FROM cart 
    WHERE user_id = $user_id AND product_id = $product_id
");

if ($check && mysqli_num_rows($check) > 0) {
    // increase quantity
    mysqli_query($conn, "
        UPDATE cart 
        SET quantity = quantity + 1 
        WHERE user_id = $user_id AND product_id = $product_id
    ");
} else {
    // insert new item
    mysqli_query($conn, "
        INSERT INTO cart (user_id, product_id, quantity)
        VALUES ($user_id, $product_id, 1)
    ");
}

// go back to previous page if available, otherwise to products
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
header("Location: $redirect");
exit();
?>
