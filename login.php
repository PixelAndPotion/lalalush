<?php
// login.php
// Handles user login and session creation

require_once 'config.php';

// ENSURE SESSION IS ACTIVE (fixes blank page issues)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Safe input handling
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {

        // Correct prepared statement with two placeholders
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check password
            if (password_verify($password, $user['password_hash'])) {

                // SET SESSION (matches DB schema)
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                // Redirect by role
                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: index.php');
                }
                exit();

            } else {
                $error = 'Incorrect password.';
            }

        } else {
            $error = 'User not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | La La Lush</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<div class="main-content">

    <div class="form-container">

        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Username or Email</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;">
                Login
            </button>

        </form>

        <p style="text-align:center; margin-top:15px;">
            Don't have an account?
            <a href="register.php">Register</a>
        </p>

    </div>

</div>

<footer>
    <p>&copy; <?= date('Y') ?> La La Lush</p>
</footer>

</body>
</html>