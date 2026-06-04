<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$order_id = (int)$_GET['id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="container" style="text-align:center; padding:50px;">

    <h2>Order Placed Successfully</h2>

    <p>Your order ID is:</p>
    <h3>#<?= $order_id ?></h3>

    <a href="products.php" class="btn-primary">
        Continue Shopping
    </a>

</div>

</body>
</html>