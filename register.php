<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $full_name = trim(mysqli_real_escape_string($conn, $_POST['full_name']));
    $password = $_POST['password'];

    // NEW: role selection
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : 'buyer';

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        // check duplicates
        $check = mysqli_query($conn, "
            SELECT user_id FROM users 
            WHERE username='$username' OR email='$email'
        ");

        if (mysqli_num_rows($check) > 0) {
            $error = "Username or email already exists.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insert = mysqli_query($conn, "
                INSERT INTO users 
                (username, email, full_name, password_hash, role)
                VALUES 
                ('$username', '$email', '$full_name', '$hash', '$role')
            ");

            if ($insert) {
                $success = "Account created successfully. You can now log in.";
            } else {
                $error = "Error creating account.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<form method="POST">

    <input name="username" placeholder="Username" required><br>
    <input name="email" placeholder="Email" required><br>
    <input name="full_name" placeholder="Full Name"><br>
    <input type="password" name="password" placeholder="Password" required><br>

    <!-- NEW ROLE OPTION -->
    <select name="role" required>
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
    </select><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>