<?php
// This page allows administrators to manage all customer orders
// Admin users can view order details and update order statuses
// Payment status and tracking numbers can also be updated here
// This is part of the RBAC protected admin system

// Load main configuration file using absolute path to avoid path issues
require_once dirname(__DIR__) . '/config.php';

// Ensure session is active before checking role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security check to make sure only admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle update request when admin submits form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {

    // Get order ID from hidden form field
    $order_id = (int)$_POST['order_id'];

    // Get updated order status
    $order_status = mysqli_real_escape_string($conn, $_POST['order_status']);

    // Get updated payment status
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);

    // Get tracking number (can be empty)
    $tracking_number = mysqli_real_escape_string($conn, trim($_POST['tracking_number']));

    // Update order in database
    $update_sql = "
        UPDATE orders
        SET order_status = '$order_status',
            payment_status = '$payment_status',
            tracking_number = '$tracking_number'
        WHERE order_id = $order_id
    ";

    mysqli_query($conn, $update_sql);

    // Redirect to prevent form resubmission on refresh
    header('Location: orders.php?updated=1');
    exit();
}

// Fetch all orders with customer details for admin view
// Join users table to display customer information
$orders = mysqli_query($conn, "
    SELECT o.*, u.username, u.full_name, u.email, u.phone
    FROM orders o
    JOIN users u ON o.buyer_id = u.user_id
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin</title>

    <!-- Main site styling -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Admin styling -->
    <link rel="stylesheet" href="css/admin-style.css">

    <!-- Bootstrap for responsive layout -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="admin-layout">

    <!-- Admin sidebar navigation -->
    <div class="admin-sidebar">
        <div class="admin-logo">
            La La Lush 🌸
            <br>
            <small style="font-size:0.7rem; font-weight:400; color:#f8bbd0;">
                Admin Panel
            </small>
        </div>

        <nav>
            <a href="index.php" class="admin-nav-link">Dashboard</a>
            <a href="orders.php" class="admin-nav-link">Orders</a>
            <a href="products.php" class="admin-nav-link">Products</a>
            <a href="users.php" class="admin-nav-link">Users</a>
            <a href="../index.php" class="admin-nav-link">Main Site</a>
            <a href="../logout.php" class="admin-nav-link">Logout</a>
        </nav>
    </div>

    <div class="admin-content">

        <!-- Page title -->
        <h2 style="color:#c2185b; margin-bottom:25px; font-weight:700;">
            Manage Orders
        </h2>

        <!-- Success message after update -->
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success" style="max-width:500px;">
                Order updated successfully
            </div>
        <?php endif; ?>

        <!-- Loop through all orders -->
        <?php while ($o = mysqli_fetch_assoc($orders)): ?>

            <div style="background:#fff; border-radius:16px; padding:25px; margin-bottom:20px; box-shadow:0 4px 15px rgba(0,0,0,0.06);">

                <!-- Order summary and customer info -->
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:15px;">

                    <div>
                        <h5>Order #<?= $o['order_id'] ?></h5>

                        <p style="color:#999; font-size:0.85rem;">
                            <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
                        </p>

                        <p style="font-size:0.9rem;">
                            Customer:
                            <strong><?= htmlspecialchars($o['full_name']) ?></strong>
                            (<?= htmlspecialchars($o['email']) ?>)
                        </p>

                        <p style="font-size:0.9rem;">
                            Phone: <?= htmlspecialchars($o['phone']) ?>
                        </p>

                        <p style="font-size:0.9rem;">
                            Shipping:
                            <?= htmlspecialchars($o['shipping_address']) ?>,
                            <?= htmlspecialchars($o['shipping_city']) ?>,
                            <?= htmlspecialchars($o['shipping_province']) ?>
                        </p>
                    </div>

                    <div style="text-align:right;">
                        <p style="font-size:1.3rem; font-weight:700; color:#e75480;">
                            R <?= number_format($o['total_amount'], 2) ?>
                        </p>

                        <p style="font-size:0.85rem;">
                            Payment: <?= strtoupper($o['payment_method']) ?>
                        </p>
                    </div>

                </div>

                <!-- Update order form -->
                <form method="POST" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">

                    <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">

                    <!-- Order status -->
                    <div>
                        <label>Order Status</label>
                        <select name="order_status">
                            <?php foreach (['pending','processing','dispatched','delivered','cancelled'] as $status): ?>
                                <option value="<?= $status ?>" <?= $o['order_status'] === $status ? 'selected' : '' ?>>
                                    <?= ucfirst($status) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Payment status -->
                    <div>
                        <label>Payment Status</label>
                        <select name="payment_status">
                            <?php foreach (['pending','paid','failed'] as $pstatus): ?>
                                <option value="<?= $pstatus ?>" <?= $o['payment_status'] === $pstatus ? 'selected' : '' ?>>
                                    <?= ucfirst($pstatus) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tracking number -->
                    <div>
                        <label>Tracking Number</label>
                        <input type="text"
                               name="tracking_number"
                               value="<?= htmlspecialchars($o['tracking_number'] ?? '') ?>">
                    </div>

                    <!-- Submit update -->
                    <button type="submit" name="update_order" class="btn-primary">
                        Update
                    </button>

                </form>

            </div>

        <?php endwhile; ?>

    </div>

</div>

<script src="../js/main.js"></script>
</body>
</html>