<?php
// Admin Users Management page for La La Lush
// Allows admins to view all users, change roles, and delete accounts
// Implements Role-Based Access Control (RBAC)

require_once dirname(__DIR__) . '/config.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
// Only admin role users can manage other users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle role update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $target_uid = (int)$_POST['target_uid'];
    $new_role   = mysqli_real_escape_string($conn, $_POST['new_role']);

    // Prevent admin from accidentally changing their own role
    if ($target_uid !== (int)$_SESSION['user_id']) {
        mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE user_id=$target_uid");
    }

    header('Location: users.php?updated=1');
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $del_uid = (int)$_GET['delete'];

    // Prevent admin from deleting their own account
    if ($del_uid !== (int)$_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE user_id=$del_uid");
    }

    header('Location: users.php?deleted=1');
    exit();
}

// Fetch all users ordered by most recently registered
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users | La La Lush</title>
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

        <h2 style="color:#c2185b; margin-bottom:25px; font-weight:700;">Manage Users</h2>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success" style="max-width:400px;">User role updated successfully.</div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success" style="max-width:400px;">User deleted successfully.</div>
        <?php endif; ?>

        <div style="overflow-x:auto;">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($u = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?= $u['user_id'] ?></td>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <!-- Inline role change form for each user -->
                            <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                <input type="hidden" name="target_uid" value="<?= $u['user_id'] ?>">
                                <select name="new_role" style="padding:5px 8px; border:1.5px solid #ddd; border-radius:6px; font-family:Poppins,sans-serif; font-size:0.82rem;">
                                    <option value="customer" <?= $u['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                    <option value="seller"   <?= $u['role'] === 'seller'   ? 'selected' : '' ?>>Seller</option>
                                    <option value="admin"    <?= $u['role'] === 'admin'    ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role"
                                        style="background:#e75480; color:#fff; border:none; padding:5px 10px; border-radius:6px; font-size:0.78rem; cursor:pointer;">
                                    Save
                                </button>
                            </form>
                        </td>
                        <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <!-- Prevent admin from deleting their own account -->
                            <?php if ($u['user_id'] !== (int)$_SESSION['user_id']): ?>
                                <a href="users.php?delete=<?= $u['user_id'] ?>"
                                   onclick="return confirm('Delete this user? This cannot be undone.')"
                                   style="color:#e53935; font-size:0.82rem; text-decoration:none;">
                                    Delete
                                </a>
                            <?php else: ?>
                                <span style="color:#bbb; font-size:0.82rem;">You</span>
                            <?php endif; ?>
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