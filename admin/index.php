<?php
// This file is the Admin Dashboard page for La La Lush
// The dashboard gives admins a quick overview of the entire system
// It displays statistics, recent orders, and quick navigation links

// Load the main configuration file
// dirname(__DIR__) gets the parent folder path safely
// This prevents path issues when loading config.php from inside the admin folder
require_once dirname(__DIR__) . '/config.php';

// RBAC CHECK (ADMIN ONLY)
// Check if the user is logged in
// Also check if the logged in user has admin role
// If not an admin, redirect user back to login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Temporary database connection test
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Count total users in the system
// Admin accounts are excluded from this count
$total_users = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role != 'admin'")
)['c'];

// Count all active products currently listed on the platform
$total_products = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM products WHERE is_active = 1")
)['c'];

// Count total number of orders placed by customers
$total_orders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders")
)['c'];

// Calculate total revenue from orders marked as paid
// If there are no paid orders yet, default value becomes 0
$total_revenue = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total_amount) AS s FROM orders WHERE payment_status = 'paid'")
)['s'] ?? 0;

// Fetch the latest 5 orders from the database
// Join users table so customer details can also be displayed
$recent_orders = mysqli_query($conn, "
    SELECT o.*, u.username, u.full_name
    FROM orders o
    JOIN users u ON o.buyer_id = u.user_id
    ORDER BY o.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | La La Lush</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../admin/css/admin-style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

</head>

<body>

<div class="admin-layout">

    <div class="admin-sidebar">

        <div class="admin-logo">
            La La Lush 🌸 <br>
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
            <a href="../logout.php" class="admin-nav-link" style="color:#f48fb1;">Logout</a>
        </nav>

    </div>

    <div class="admin-content">

        <h2 style="color:#c2185b; margin-bottom:30px; font-weight:700;">
            Dashboard Overview
        </h2>

        <div class="row g-4 mb-5">

            <div class="col-6 col-md-3">
                <div class="stat-card" style="border-left:5px solid #e75480;">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number"><?= $total_users ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card" style="border-left:5px solid #9c27b0;">
                    <div class="stat-icon">🛍️</div>
                    <div class="stat-number"><?= $total_products ?></div>
                    <div class="stat-label">Active Products</div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card" style="border-left:5px solid #1976d2;">
                    <div class="stat-icon">📦</div>
                    <div class="stat-number"><?= $total_orders ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="stat-card" style="border-left:5px solid #2e7d32;">
                    <div class="stat-icon">💰</div>
                    <div class="stat-number">R <?= number_format($total_revenue, 0) ?></div>
                    <div class="stat-label">Revenue (Paid)</div>
                </div>
            </div>

        </div>

        <h4 style="color:#333; margin-bottom:15px;">
            Recent Orders
        </h4>

        <div style="overflow-x:auto;">

            <table class="cart-table">

                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($ord = mysqli_fetch_assoc($recent_orders)): ?>

                    <tr>

                        <td>#<?= $ord['order_id'] ?></td>

                        <td>
                            <?= htmlspecialchars($ord['full_name']) ?><br>
                            <small style="color:#999;">
                                <?= htmlspecialchars($ord['username']) ?>
                            </small>
                        </td>

                        <td>R <?= number_format($ord['total_amount'], 2) ?></td>

                        <td><?= strtoupper($ord['payment_method']) ?></td>

                        <td>
                            <span style="background:#e75480; color:#fff; padding:3px 10px; border-radius:12px; font-size:0.78rem;">
                                <?= $ord['order_status'] ?>
                            </span>
                        </td>

                        <td><?= date('d M Y', strtotime($ord['created_at'])) ?></td>

                        <td>
                            <a href="orders.php?id=<?= $ord['order_id'] ?>" style="color:#e75480;">
                                Manage
                            </a>
                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        </div>

        <div style="display:flex; gap:15px; margin-top:30px; flex-wrap:wrap;">
            <a href="orders.php" class="btn-primary">Manage All Orders</a>
            <a href="products.php" class="btn-secondary">Manage Products</a>
            <a href="users.php" class="btn-secondary">Manage Users</a>
        </div>

    </div>

</div>

<script src="../js/main.js"></script>

</body>
</html>