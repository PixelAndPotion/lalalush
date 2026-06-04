<?php
// This page allows sellers to view and manage their own products
// Part of seller dashboard functionality in the C2C system

require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only sellers allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: index.php');
    exit();
}

$seller_id = (int)$_SESSION['user_id'];

// Fetch seller products only
$products = mysqli_query($conn, "
    SELECT * 
    FROM products 
    WHERE seller_id = $seller_id
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Products</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">

    <h2>My Products</h2>

    <a href="sell.php" class="btn-primary">Add New Product</a>

    <br><br>

    <?php while ($p = mysqli_fetch_assoc($products)): ?>

        <div style="background:#fff; padding:15px; margin-bottom:10px; border-radius:10px;">

            <h4><?= htmlspecialchars($p['product_name']) ?></h4>

            <p>Price: R <?= number_format($p['price'], 2) ?></p>
            <p>Stock: <?= $p['stock_quantity'] ?></p>

            <p>Status:
                <?= $p['is_active'] ? 'Active' : 'Hidden' ?>
            </p>

        </div>

    <?php endwhile; ?>

</div>

</body>
</html>