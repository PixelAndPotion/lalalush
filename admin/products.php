<?php
// Admin Products Management page
// Allows admin to view, toggle visibility, and delete products

require_once dirname(__DIR__) . '/config.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin access check
// Only users with admin role can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Toggle product visibility between active and inactive
if (isset($_GET['toggle'])) {
    $pid = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE products SET is_active = NOT is_active WHERE product_id = $pid");
    header('Location: products.php');
    exit();
}

// Delete a product permanently from the database
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE product_id = $pid");
    header('Location: products.php?deleted=1');
    exit();
}

// Fetch all products with their category and seller details
$products = mysqli_query($conn, "
    SELECT p.*, c.category_name, u.username AS seller
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    JOIN users u ON p.seller_id = u.user_id
    ORDER BY p.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products | La La Lush</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="admin-layout">

    <div class="admin-sidebar">
        <div class="admin-logo">
            La La Lush 🌸<br>
            <small style="font-size:0.7rem; font-weight:400; color:#f8bbd0;">Admin Panel</small>
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

        <h2 style="color:#c2185b; margin-bottom:25px; font-weight:700;">Manage Products</h2>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success" style="max-width:400px;">Product deleted successfully.</div>
        <?php endif; ?>

        <div style="overflow-x:auto;">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Seller</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($p = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td>
                            <img src="../images/<?= htmlspecialchars($p['product_image']) ?>"
                                 onerror="this.src='../images/no-image.png'"
                                 style="width:50px; height:50px; object-fit:cover; border-radius:8px;">
                        </td>
                        <td><?= htmlspecialchars($p['product_name']) ?></td>
                        <td><?= htmlspecialchars($p['category_name']) ?></td>
                        <td><?= htmlspecialchars($p['seller']) ?></td>
                        <td>R <?= number_format($p['price'], 2) ?></td>
                        <td><?= $p['stock_quantity'] ?></td>
                        <td>
                            <!-- Show green for active products and red for hidden ones -->
                            <span style="background:<?= $p['is_active'] ? '#2e7d32' : '#c62828' ?>; color:#fff; padding:3px 10px; border-radius:12px; font-size:0.78rem;">
                                <?= $p['is_active'] ? 'Active' : 'Hidden' ?>
                            </span>
                        </td>
                        <td style="display:flex; gap:8px; align-items:center;">
                            <!-- Toggle active or hidden status -->
                            <a href="products.php?toggle=<?= $p['product_id'] ?>"
                               style="color:#1565c0; font-size:0.82rem; text-decoration:none;">
                                <?= $p['is_active'] ? 'Hide' : 'Show' ?>
                            </a>
                            <!-- Delete product with confirmation prompt -->
                            <a href="products.php?delete=<?= $p['product_id'] ?>"
                               onclick="return confirm('Delete this product permanently?')"
                               style="color:#e53935; font-size:0.82rem; text-decoration:none;">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script src="../js/main.js"></script>
</body>
</html>