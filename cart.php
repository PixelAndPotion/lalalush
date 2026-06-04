<?php
require_once 'config.php';

// Must be logged in to view cart
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Remove item from cart
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];

    mysqli_query($conn, "
        DELETE FROM cart 
        WHERE cart_id = $cart_id AND user_id = $user_id
    ");

    header('Location: cart.php');
    exit();
}

// Load cart items
$cart = mysqli_query($conn, "
    SELECT c.cart_id, c.quantity, 
           p.product_name, p.price, p.product_image
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = $user_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">

    <h2>Your Shopping Cart</h2>

    <?php if (mysqli_num_rows($cart) > 0): ?>

        <?php $total = 0; ?>

        <?php while ($item = mysqli_fetch_assoc($cart)): ?>

            <?php $subtotal = $item['price'] * $item['quantity']; ?>
            <?php $total += $subtotal; ?>

            <div class="cart-item">
                
                <img src="images/<?= htmlspecialchars($item['product_image']) ?>" width="80">

                <div>
                    <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                    <p>Price: R <?= number_format($item['price'], 2) ?></p>
                    <p>Quantity: <?= $item['quantity'] ?></p>
                    <p>Subtotal: R <?= number_format($subtotal, 2) ?></p>

                    <a href="cart.php?remove=<?= $item['cart_id'] ?>">
                        Remove
                    </a>
                </div>

            </div>

        <?php endwhile; ?>

        <hr>

        <h3>Total: R <?= number_format($total, 2) ?></h3>

        <a href="checkout.php" class="btn-primary">Checkout</a>

    <?php else: ?>

        <p>Your cart is empty.</p>

    <?php endif; ?>

</div>

</body>
</html>