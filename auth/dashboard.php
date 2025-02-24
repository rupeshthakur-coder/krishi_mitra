<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>

<body>
    <h2>Welcome, <?php echo $_SESSION["user_name"]; ?>!</h2>
    <p><strong>Email:</strong> <?php echo $_SESSION["user_email"]; ?></p>
    <p><strong>User Type:</strong> <?php echo $_SESSION["user_type"]; ?></p>

    <hr>

    <!-- Dashboard Navigation Links -->
    <h3>Manage Your Account</h3>
    <ul>
        <li><a href="profile.php">Edit Profile</a></li>
        <li><a href="add_product.php">Add New Product</a></li>
        <li><a href="orders.php">My Orders</a></li>
        <li><a href="reviews.php">Manage Reviews</a></li>
    </ul>

    <hr>

    <!-- Logout -->
    <a href="logout.php">Logout</a>
</body>

</html>