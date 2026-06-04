<?php
// This file is the reusable navigation bar used across the entire La La Lush website
// It is included on every page so users always see the same navigation structure
// It also changes based on whether the user is logged in and their role
// It relies on config.php being loaded first so that sessions and database connection exist
?>

<nav class="navbar">
    <a href="index.php" class="logo">La La Lush 🌸</a>

    <ul class="nav-links" id="nav-menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Shop</a></li>

        <?php
        // Check if the user is logged in by verifying session data
        // If user_id exists in the session, it means the user is authenticated
        if (isset($_SESSION['user_id'])):
        ?>

            <li><a href="orders.php">My Orders</a></li>

            <a href="https://wa.me/27723025838?text=Hi%20La%20La%20Lush%2C%20I%20need%20help%20with%20an%20order"
   target="_blank"
   style="
       display:inline-block;
       background:#25D366;
       color:#fff;
       padding:10px 16px;
       border-radius:8px;
       text-decoration:none;
       font-weight:600;
   ">
    Chat with Support
</a>

            <?php
            // Only show the Sell page if the user has seller or admin privileges
            // This ensures that only authorized users can access product selling features
            if ($_SESSION['role'] === 'seller' || $_SESSION['role'] === 'admin'):
            ?>
                <li><a href="sell.php">Sell</a></li>
            <?php endif; ?>

            <?php
            // Only admin users can see and access the admin dashboard link
            // This protects administrative functions from normal users
            if ($_SESSION['role'] === 'admin'):
            ?>
                <li><a href="admin/index.php" style="color:#c2185b; font-weight:700;">Admin</a></li>
            <?php endif; ?>

            <li><a href="logout.php">Logout</a></li>

        <?php else: ?>

            <?php
            // If the user is not logged in, show authentication options instead
            // These links allow users to either sign in or create a new account
            ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>

        <?php endif; ?>

        <li>
            <a href="cart.php">🛒
                <?php
                // Display the number of items in the user's cart
                // This only runs if the user is logged in and database connection exists
                if (isset($_SESSION['user_id']) && isset($conn)) {

                    // Convert user_id to integer for security purposes
                    // This prevents SQL injection risks in the query
                    $userId = (int)$_SESSION['user_id'];

                    // Query the database to calculate total quantity of items in the cart
                    $query = mysqli_query(
                        $conn,
                        "SELECT SUM(quantity) AS total FROM cart WHERE user_id = $userId"
                    );

                    // Check if query executed successfully before using results
                    if ($query) {
                        $data = mysqli_fetch_assoc($query);

                        // Only display badge if cart has items
                        if (!empty($data['total'])) {
                            echo '<span class="cart-badge">' . $data['total'] . '</span>';
                        }
                    }
                }
                ?>
            </a>
        </li>
    </ul>
</nav>