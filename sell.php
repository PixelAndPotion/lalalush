<?php
// Sell page
// Allows sellers to add products to the marketplace
// Uploads product images
// Saves products to the database

require_once 'config.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure user is a seller or admin
if (
    !isset($_SESSION['role']) ||
    ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'admin')
) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form values
    $name = mysqli_real_escape_string(
        $conn,
        trim($_POST['product_name'])
    );

    $price = (float)$_POST['price'];

    $category = (int)$_POST['category'];

    $stock = (int)$_POST['stock_quantity'];

    $seller = (int)$_SESSION['user_id'];

    $image = '';

    // Handle image upload
    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] === 0
    ) {

        $image = basename($_FILES['image']['name']);

        $target = "images/products/" . $image;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $target
        );
    }

    // Validate required fields
    if (
        empty($name) ||
        $price <= 0 ||
        $stock < 0
    ) {

        $error = "Please complete all fields correctly.";

    } else {

        // Insert product
        $sql = "
        INSERT INTO products
        (
            seller_id,
            category_id,
            product_name,
            price,
            stock_quantity,
            product_image,
            is_active
        )
        VALUES
        (
            $seller,
            $category,
            '$name',
            $price,
            $stock,
            '$image',
            1
        )
        ";

        if (mysqli_query($conn, $sql)) {

            $success = "Product added successfully.";

        } else {

            $error = mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sell Product</title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container">

    <h2>Sell a Product</h2>

    <?php if ($error): ?>
        <p style="color:red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;">
            <?= htmlspecialchars($success) ?>
        </p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Product Name</label>
        <input
            type="text"
            name="product_name"
            required
        >

        <br><br>

        <label>Price</label>
        <input
            type="number"
            step="0.01"
            name="price"
            required
        >

        <br><br>

        <label>Stock Quantity</label>
        <input
            type="number"
            name="stock_quantity"
            required
        >

        <br><br>

        <label>Category</label>

        <select name="category" required>
            <option value="1">Candles</option>
            <option value="2">Soaps</option>
            <option value="3">Bath Salts</option>
        </select>

        <br><br>

        <label>Product Image</label>
        <input
            type="file"
            name="image"
            accept="image/*"
        >

        <br><br>

        <button type="submit">
            Sell Product
        </button>

    </form>

</div>

</body>
</html>