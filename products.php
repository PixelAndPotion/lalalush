<?php
// This page displays all products in the La La Lush C2C marketplace
// It supports both official La La Lush products and seller-listed products
// Users can browse, search, and filter products by category

require_once 'config.php';

// Get category filter from URL
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Get search input
$search = isset($_GET['search'])
    ? mysqli_real_escape_string($conn, trim($_GET['search']))
    : '';

// Main product query
// Includes category name, seller name (C2C system), and review rating
$sql = "SELECT p.*, 
        c.category_name,
        u.username AS seller,
        (SELECT AVG(rating) FROM reviews WHERE product_id = p.product_id) AS avg_rating
        FROM products p
        JOIN categories c ON p.category_id = c.category_id
        JOIN users u ON p.seller_id = u.user_id
        WHERE p.is_active = 1";

// Apply category filter
if ($category_filter > 0) {
    $sql .= " AND p.category_id = $category_filter";
}

// Apply search filter
$sql .= " AND (
    p.product_name LIKE '%$search%' OR
    p.scent LIKE '%$search%' OR
    p.colour LIKE '%$search%'
)";

// Order newest first
$sql .= " ORDER BY p.created_at DESC";

$result = mysqli_query($conn, $sql);

// Load categories for filter buttons
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | La La Lush</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="main-content container mt-4">

    <h2 class="section-title">Our Products</h2>

    <div class="text-center mb-4">
        <form method="GET" style="display:inline-flex; gap:10px; width:100%; max-width:500px;">

            <input type="text"
                   name="search"
                   value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search products..."
                   style="padding:10px; border:1px solid #ddd; border-radius:20px; flex:1;">

            <?php if ($category_filter): ?>
                <input type="hidden" name="category" value="<?= $category_filter ?>">
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <div class="text-center mb-4">

        <a href="products.php"
           class="<?= $category_filter === 0 ? 'btn btn-primary' : 'btn btn-secondary' ?>">
            All
        </a>

        <?php mysqli_data_seek($categories, 0); ?>

        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
            <a href="products.php?category=<?= $cat['category_id'] ?>"
               class="<?= $category_filter === (int)$cat['category_id'] ? 'btn btn-primary' : 'btn btn-secondary' ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
            </a>
        <?php endwhile; ?>

    </div>

    <div class="products-grid">

        <?php if (mysqli_num_rows($result) > 0): ?>

            <?php while ($p = mysqli_fetch_assoc($result)): ?>

                <a href="add-to-cart.php?id=<?= $p['product_id'] ?>" class="product-card">

                    <img src="images/products/<?= htmlspecialchars($p['product_image']) ?>"
                         alt="<?= htmlspecialchars($p['product_name']) ?>"
                         onerror="this.src='images/no-image.png'">

                    <div class="card-body">

                        <h3><?= htmlspecialchars($p['product_name']) ?></h3>

                        <p><?= htmlspecialchars($p['category_name']) ?></p>

                        <?php if ($p['seller']): ?>
                            <p>
                                Seller: <?= htmlspecialchars($p['seller']) ?>
                                <?php if ($p['seller'] !== 'La La Lush Official'): ?>
                                    (Unverified Seller)
                                <?php else: ?>
                                    (Official Store)
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <p>R <?= number_format($p['price'], 2) ?></p>

                        <?php if ($p['scent']): ?>
                            <p>Scent: <?= htmlspecialchars($p['scent']) ?></p>
                        <?php endif; ?>

                        <?php if ($p['stock_quantity'] <= 0): ?>
                            <p style="color:red;">Out of Stock</p>
                        <?php endif; ?>

                    </div>

                </a>

            <?php endwhile; ?>

        <?php else: ?>

            <div style="text-align:center; padding:40px; color:#777;">
                No products found.
            </div>

        <?php endif; ?>

    </div>

</div>

<footer>
    <p>&copy; <?= date('Y') ?> La La Lush</p>
</footer>

</body>
</html>