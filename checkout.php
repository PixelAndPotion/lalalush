<?php
// Checkout page
// Handles order creation, order items, stock updates, and cart clearing

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$uid = (int)$_SESSION['user_id'];

// Load cart items
$cart_sql = "
SELECT 
    c.cart_id,
    c.quantity,
    p.product_id,
    p.product_name,
    p.price
FROM cart c
JOIN products p ON c.product_id = p.product_id
WHERE c.user_id = $uid
";

$cart_items = mysqli_query($conn, $cart_sql);

// STOP if SQL fails (prevents blank screen)
if (!$cart_items) {
    die("Cart error: " . mysqli_error($conn));
}

// Redirect if empty cart
if (mysqli_num_rows($cart_items) === 0) {
    header('Location: cart.php');
    exit();
}

// Build cart + total
$items = [];
$total = 0;

while ($row = mysqli_fetch_assoc($cart_items)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $total += $row['line_total'];
    $items[] = $row;
}

$shipping = 85;
$grand_total = $total + $shipping;

$error = '';

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $province = mysqli_real_escape_string($conn, $_POST['province']);
    $payment = mysqli_real_escape_string($conn, $_POST['payment_method']);

    if ($address === '' || $city === '' || $province === '') {
        $error = "Please fill in all shipping details.";
    } else {

        // Create order
        $order_sql = "
        INSERT INTO orders 
        (buyer_id, total_amount, shipping_address, shipping_city, shipping_province, payment_method, order_status)
        VALUES 
        ($uid, $grand_total, '$address', '$city', '$province', '$payment', 'pending')
        ";

        if (!mysqli_query($conn, $order_sql)) {
            die("Order insert failed: " . mysqli_error($conn));
        }

        $order_id = mysqli_insert_id($conn);

        // Insert order items + update stock
        foreach ($items as $item) {

            $pid = (int)$item['product_id'];
            $qty = (int)$item['quantity'];
            $price = (float)$item['price'];

            mysqli_query($conn, "
                INSERT INTO order_items 
                (order_id, product_id, quantity, unit_price)
                VALUES 
                ($order_id, $pid, $qty, $price)
            ");

            mysqli_query($conn, "
                UPDATE products 
                SET stock_quantity = stock_quantity - $qty
                WHERE product_id = $pid
            ");
        }

        // Clear cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $uid");

        // Redirect to success page
        header("Location: order-success.php?id=$order_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">

    <h2>Checkout</h2>

    <h3>Total: R <?= number_format($grand_total, 2) ?></h3>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">

        <input name="shipping_address" placeholder="Address" required><br><br>
        <input name="city" placeholder="City" required><br><br>
        <input name="province" placeholder="Province" required><br><br>

        <select name="payment_method">
            <option value="card">Card</option>
            <option value="eft">EFT</option>
        </select><br><br>

        <button type="submit">Place Order</button>

        <div style="margin-top:15px; text-align:center;">
    <a href="https://wa.me/27723025838?text=Hi%20La%20La%20Lush%2C%20I%20need%20help%20with%20my%20checkout"
       target="_blank"
       style="
           display:inline-block;
           background:#25D366;
           color:#fff;
           padding:10px 14px;
           border-radius:8px;
           text-decoration:none;
           font-weight:600;
       ">
        Need Help? Chat on WhatsApp
    </a>
</div>

    </form>

</div>

</body>
</html>