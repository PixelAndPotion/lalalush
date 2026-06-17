<?php
$hash = '$2y$10$GFan0CjhwySfoEzitB4WSuQi4gv8eJhAXM6OKsqZEbX2MB0Oy47i.';
if (password_verify('admin123', $hash)) {
    echo "Password works!";
} else {
    echo "Password failed!";
}
?>
