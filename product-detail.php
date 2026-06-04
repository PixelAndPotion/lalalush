<?php
session_start();
require_once 'config.php';

// Get product ID safely
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    header('Location: products.php');
    exit();
}

// Load product with category + seller info
$query = "SELECT p.*, c.category_name, 
          u.username AS seller_name, 
          u.full_name AS seller_fullname
          FROM products p
          JOIN categories c ON p.category_id = c.category_id
          JOIN users u ON p.seller_id = u.user_id
          WHERE p.product_id = $product_id
          LIMIT 1";

$result = mysqli_query($conn, $query);

// If query fails OR no product found, redirect safely
if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: products.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Messages
$cart_msg = '';
$review_msg = '';

// Handle Add to Cart (form POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $qty = max(1, (int)$_POST['quantity']);
    $uid = (int)$_SESSION['user_id'];

    $existing = mysqli_query($conn, "
        SELECT cart_id, quantity 
        FROM cart 
        WHERE user_id=$uid AND product_id=$product_id
    ");

    if ($existing && mysqli_num_rows($existing) > 0) {
        $row = mysqli_fetch_assoc($existing);
        $new_qty = $row['quantity'] + $qty;
        mysqli_query($conn, "
            UPDATE cart 
            SET quantity=$new_qty 
            WHERE cart_id=" . (int)$row['cart_id']
        );
    } else {
        mysqli_query($conn, "
            INSERT INTO cart (user_id, product_id, quantity)
            VALUES ($uid, $product_id, $qty)
        ");
    }

    $cart_msg = "Added to cart successfully";
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        $rating = (int)$_POST['rating'];
        $review_text = mysqli_real_escape_string($conn, trim($_POST['review_text']));

        if ($rating >= 1 && $rating <= 5) {
            $check_review = mysqli_query($conn, "
                SELECT review_id 
                FROM reviews 
                WHERE user_id=$uid AND product_id=$product_id
            ");

            if ($check_review && mysqli_num_rows($check_review) === 0) {
                mysqli_query($conn, "
                    INSERT INTO reviews (product_id, user_id, rating, review_text)
                    VALUES ($product_id, $uid, $rating, '$review_text')
                ");
                $review_msg = "Review submitted successfully";
            } else {
                $review_msg = "You have already reviewed this product";
            }
        } else {
            $review_msg = "Please select a valid rating";
        }
    } else {
        header('Location: login.php');
        exit();
    }
}

// Load reviews
$reviews = mysqli_query(
    $conn,
    "SELECT r.*, u.username
     FROM reviews r
     JOIN users u ON r.user_id = u.user_id
     WHERE r.product_id = $product_id
     ORDER BY r.created_at DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($product['product_name']) ?> - La La Lush</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-detail { max-width: 1000px; margin: 30px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .product-detail img { width: 100%; border-radius: 12px; object-fit: cover; max-height: 520px; }
        .product-info h2 { margin-bottom: 10px; color: #c2185b; }
        .product-info p { margin-bottom: 12px; color: #555; }
        .add-cart { margin-top: 18px; }
        .msg { padding: 10px 14px; background: #e8f7ef; color: #1b5e20; border-radius: 8px; margin-bottom: 12px; }
        .reviews { margin-top: 24px; }
        .review { border-top: 1px solid #eee; padding: 12px 0; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="product-detail">
    <div>
        <img src="images/products/<?= htmlspecialchars($product['product_image']) ?>"
             alt="<?= htmlspecialchars($product['product_name']) ?>"
             onerror="this.src='images/no-image.png'">
    </div>

    <div class="product-info">
        <h2><?= htmlspecialchars($product['product_name']) ?></h2>
        <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
        <p><strong>Seller:</strong> <?= htmlspecialchars($product['seller_name']) ?></p>
        <p class="price">R <?= number_format($product['price'], 2) ?></p>

        <?php if ($cart_msg): ?>
            <div class="msg"><?= htmlspecialchars($cart_msg) ?></div>
        <?php endif; ?>

        <form method="post" class="add-cart" action="">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" style="width:80px; margin-left:8px;">
            <button type="submit" name="add_to_cart" class="btn-primary" style="margin-left:12px;">Add to Cart</button>
        </form>

        <div class="reviews">
            <h3>Reviews</h3>

            <?php if ($review_msg): ?>
                <div class="msg"><?= htmlspecialchars($review_msg) ?></div>
            <?php endif; ?>

            <?php if ($reviews && mysqli_num_rows($reviews) > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
                    <div class="review">
                        <strong><?= htmlspecialchars($r['username']) ?></strong>
                        <span style="color:#e75480; margin-left:8px;">Rating: <?= (int)$r['rating'] ?>/5</span>
                        <p><?= nl2br(htmlspecialchars($r['review_text'])) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to review this product.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" style="margin-top:12px;">
                    <label for="rating">Your rating</label>
                    <select name="rating" id="rating" required style="margin-left:8px;">
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Very good</option>
                        <option value="3">3 - Good</option>
                        <option value="2">2 - Fair</option>
                        <option value="1">1 - Poor</option>
                    </select>
                    <div style="margin-top:8px;">
                        <textarea name="review_text" rows="3" placeholder="Write your review (optional)" style="width:100%; max-width:420px;"></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn-secondary" style="margin-top:8px;">Submit Review</button>
                </form>
            <?php else: ?>
                <p><a href="login.php" class="btn-primary">Log in</a> to leave a review or add to cart.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> La La Lush</p>
</footer>

</body>
</html>
