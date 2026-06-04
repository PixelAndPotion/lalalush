<?php
// This page displays the logged-in user's order history
// It shows order summary, status, payment details, and tracking information
// Part of the customer order management system in the e-commerce platform

require_once 'config.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure only logged-in users can access this page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Store logged-in user ID for database queries
$uid = (int)$_SESSION['user_id'];

// Fetch all orders for this user
// Includes item count using a subquery for display purposes
$orders = mysqli_query($conn, "
    SELECT 
        o.*,
        (SELECT COUNT(*) 
         FROM order_items 
         WHERE order_id = o.order_id) AS item_count
    FROM orders o
    WHERE o.buyer_id = $uid
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | La La Lush</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="main-content container mt-5">

    <h2 class="section-title">My Orders</h2>

    <?php if (mysqli_num_rows($orders) > 0): ?>

        <?php while ($order = mysqli_fetch_assoc($orders)): ?>

            <?php
            // order status colours for visual tracking
            $status_colors = [
                'pending'    => '#f57f17',
                'processing' => '#1565c0',
                'shipped'    => '#6a1b9a',
                'delivered'  => '#2e7d32',
                'cancelled'  => '#c62828'
            ];

            // Set default colour if status is unknown
            $color = $status_colors[$order['order_status']] ?? '#666';
            ?>

            <div style="background:#fff; padding:20px; margin-bottom:20px; border-radius:12px; box-shadow:0 3px 12px rgba(0,0,0,0.08);">

                <!-- Order header section -->
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">

                    <div>
                        <h5>Order #<?= $order['order_id'] ?></h5>
                        <small>
                            <?= date('d M Y H:i', strtotime($order['created_at'])) ?>
                        </small>
                    </div>

                    <div>
                        <span style="background:<?= $color ?>; color:#fff; padding:5px 12px; border-radius:20px; font-size:0.8rem;">
                            <?= $order['order_status'] ?>
                        </span>
                    </div>

                </div>

                <hr>

                <!-- Order details section -->
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap;">

                    <div>
                        <p><strong>Items:</strong> <?= $order['item_count'] ?></p>
                        <p><strong>Payment:</strong> <?= strtoupper($order['payment_method']) ?></p>

                        <p>
                            <strong>Payment Status:</strong>
                            <span style="color:<?= $order['payment_status'] === 'paid' ? 'green' : 'orange' ?>;">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </p>
                    </div>

                    <div style="text-align:right;">
                        <h4 style="color:#e75480;">
                            R <?= number_format($order['total_amount'], 2) ?>
                        </h4>

                        <?php if (!empty($order['tracking_number'])): ?>
                            <p>
                                Tracking: <?= htmlspecialchars($order['tracking_number']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Optional order notes -->
                <?php if (!empty($order['notes'])): ?>
                    <p style="margin-top:10px; font-size:0.85rem; color:#777;">
                        Notes: <?= htmlspecialchars($order['notes']) ?>
                    </p>
                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div style="text-align:center; padding:60px; color:#999;">
            <h4>No orders yet</h4>
            <a href="products.php" class="btn-primary">Start Shopping</a>
        </div>

    <?php endif; ?>

</div>

</body>
</html>