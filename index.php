<?php
session_start();
require_once 'config.php';

// Fetch latest active products for homepage display
$query = "
SELECT p.*, c.category_name, u.username AS seller,
       (SELECT AVG(rating) FROM reviews WHERE product_id = p.product_id) AS avg_rating
FROM products p
JOIN categories c ON p.category_id = c.category_id
JOIN users u ON p.seller_id = u.user_id
WHERE p.is_active = 1
ORDER BY p.created_at DESC
LIMIT 8
";

$result = mysqli_query($conn, $query);

// Fetch categories
$categories = mysqli_query($conn, "SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>La La Lush</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* banner strip */
        .banner-img-fallback {
            display: block;
            width: 100%;
            height: 0px;          /*  height */
            object-fit: cover;     
            border-radius: 0;
            margin-bottom: 18px;
        }
        @media (max-width: 768px) {
            .banner-img-fallback {
                height: 0px;      /* smaller on mobile */
            }
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- HERO SECTION WITH BANNER IMAGE -->
<section class="hero-banner">
    <!--   fallback image, banner -->
    <img src="images/products/banner.jpg" alt="La La Lush Banner" class="banner-img-fallback">

    <h1>Handmade Products, Real Sellers</h1>
    <p>A C2C marketplace where real creators sell handmade bath, candle and wellness products directly to you.</p>

    <div style="margin-top:18px;">
        <a href="products.php" class="btn-primary">Shop Now</a>
        <a href="register.php" class="btn-secondary">Become a Seller</a>
    </div>
</section>

<!-- CATEGORIES SECTION -->
<h2 class="section-title">Browse Categories</h2>
<div class="products-grid">
<?php while ($cat = mysqli_fetch_assoc($categories)): ?>
    <a href="products.php?category=<?= (int)$cat['category_id'] ?>" class="product-card">
        <div class="card-body" style="text-align:center; padding:30px;">
            <h3>
                <?php
                $name = $cat['category_name'];
                if ($name === 'Candles') echo '🕯️ Candles';
                elseif ($name === 'Soaps') echo '🧼 Soaps';
                elseif ($name === 'Bath Salts') echo '🛁 Bath Salts';
                else echo '🛍️ ' . htmlspecialchars($name);
                ?>
            </h3>
        </div>
    </a>
<?php endwhile; ?>
</div>

<!-- FEATURED PRODUCTS -->
<h2 class="section-title">Featured Products</h2>
<div class="products-grid">
<?php while ($p = mysqli_fetch_assoc($result)): ?>
    <a href="product-detail.php?id=<?= (int)$p['product_id'] ?>" class="product-card">
        <img src="images/products/<?= htmlspecialchars($p['product_image']) ?>"
             alt="<?= htmlspecialchars($p['product_name']) ?>"
             onerror="this.src='images/no-image.png'">
        <div class="card-body">
            <h3><?= htmlspecialchars($p['product_name']) ?></h3>
            <p>Seller: <?= htmlspecialchars($p['seller']) ?></p>
            <p class="price">R <?= number_format($p['price'], 2) ?></p>
        </div>
    </a>
<?php endwhile; ?>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> La La Lush</p>
</footer>

</body>
</html>
