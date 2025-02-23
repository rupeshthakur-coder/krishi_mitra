<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
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
    <p>Email: <?php echo $_SESSION["user_email"]; ?></p>
    <p>User Type: <?php echo $_SESSION["user_type"]; ?></p>

    <a href="profile.php">Edit Profile</a>|
    <a href="logout.php">Logout</a>
</body>

</html>