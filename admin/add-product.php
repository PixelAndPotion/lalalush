<?php
// Admin Add Product Page for La La Lush
// This page allows admin to create new products and assign sellers, categories, and product details

require_once dirname(__DIR__) . '/config.php';

// Only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get categories for dropdown
$categories = mysqli_query($conn, "SELECT * FROM categories");

// Get users for seller dropdown (this enables C2C system)
$sellers = mysqli_query($conn, "SELECT user_id, username, role FROM users");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['product_name'];
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $category = $_POST['category_id'];
    $seller = $_POST['seller_id'];
    $type = $_POST['product_type'];
    $scent = $_POST['scent'];
    $official = isset($_POST['is_official']) ? 1 : 0;

    // Image upload
    $imageName = $_FILES['product_image']['name'];
    $tmp = $_FILES['product_image']['tmp_name'];

    $uploadPath = "../images/" . $imageName;
    move_uploaded_file($tmp, $uploadPath);

    // Insert into database
    mysqli_query($conn, "
        INSERT INTO products
        (product_name, price, stock_quantity, category_id, seller_id, product_image, product_type, scent, is_official, is_active)
        VALUES
        ('$name', '$price', '$stock', '$category', '$seller', '$imageName', '$type', '$scent', '$official', 1)
    ");

    header('Location: products.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>

<div class="admin-layout">

    <div class="admin-sidebar">
        <div class="admin-logo">La La Lush Admin</div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="add-product.php">Add Product</a>
        </nav>
    </div>

    <div class="admin-content">

        <h2>Add New Product</h2>

        <form method="POST" enctype="multipart/form-data">

            <label>Product Name</label>
            <input type="text" name="product_name" required>

            <label>Price</label>
            <input type="number" step="0.01" name="price" required>

            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" required>

            <label>Category</label>
            <select name="category_id">
                <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                    <option value="<?= $c['category_id'] ?>"><?= $c['category_name'] ?></option>
                <?php } ?>
            </select>

            <label>Seller (C2C)</label>
            <select name="seller_id">
                <?php while ($s = mysqli_fetch_assoc($sellers)) { ?>
                    <option value="<?= $s['user_id'] ?>">
                        <?= $s['username'] ?> (<?= $s['role'] ?>)
                    </option>
                <?php } ?>
            </select>

            <label>Product Type</label>
            <select name="product_type">
                <option value="candles">Candles</option>
                <option value="bath_salts">Bath Salts</option>
                <option value="soaps">Soaps</option>
                <option value="gift_set">Gift Set</option>
            </select>

            <label>Scent (optional)</label>
            <select name="scent">
                <option value="">None</option>
                <option value="vanilla">Vanilla</option>
                <option value="lavender">Lavender</option>
                <option value="lemongrass">Lemongrass</option>
                <option value="oreo">Oreo</option>
                <option value="cupcake">Cupcake</option>
            </select>

            <label>Official La La Lush Product</label>
            <input type="checkbox" name="is_official" value="1">

            <label>Product Image</label>
            <input type="file" name="product_image" required>

            <br><br>
            <button type="submit">Add Product</button>

        </form>

    </div>

</div>

</body>
</html>