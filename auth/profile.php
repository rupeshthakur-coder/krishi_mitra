<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
  header("Location: login.php"); // Redirect to login page if not logged in
  exit();
}

include 'config.php';

// Fetch user data from the database
$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
} else {
  echo "User not found!";
  exit();
}

// Validate password strength
function validatePassword($password)
{
  return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitizing and validating input data
  $name = htmlspecialchars(trim($_POST["name"]));
  $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
  $phone = htmlspecialchars(trim($_POST["phone"]));
  $address = htmlspecialchars(trim($_POST["address"]));

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format!";
    exit();
  }

  // Update profile details
  $update_sql = "UPDATE users SET name='$name', email='$email', phone='$phone', address='$address' WHERE id=$user_id";
  if ($conn->query($update_sql) === TRUE) {
    $_SESSION["user_name"] = $name;
    $_SESSION["user_email"] = $email;
    echo "Profile updated successfully!";
  } else {
    echo "Error updating profile: " . $conn->error;
  }

  // Handle password change
  if (isset($_POST["change_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate new password strength
    if (!validatePassword($new_password)) {
      echo "New password must be at least 8 characters, contain one uppercase letter, one number, and one special character!";
      exit();
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
      echo "New passwords do not match!";
      exit();
    }

    // Fetch current password from the database
    $sql = "SELECT password FROM users WHERE id = $user_id";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Verify current password
    if (password_verify($current_password, $user["password"])) {
      // Hash new password and update in the database
      $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
      $update_password_sql = "UPDATE users SET password='$hashed_new_password' WHERE id=$user_id";
      if ($conn->query($update_password_sql) === TRUE) {
        echo "Password changed successfully!";
      } else {
        echo "Error changing password: " . $conn->error;
      }
    } else {
      echo "Current password is incorrect!";
    }
  }

  // Handle profile picture upload
  if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES['profile_picture']['type'];
    $file_size = $_FILES['profile_picture']['size'];
    $max_size = 2 * 1024 * 1024; // 2MB limit

    if (!in_array($file_type, $allowed_types)) {
      echo "Invalid file type! Only JPG, PNG, and GIF are allowed.";
      exit();
    }

    if ($file_size > $max_size) {
      echo "File size exceeds the 2MB limit!";
      exit();
    }

    $upload_dir = 'uploads/profile_pictures/';
    $file_name = uniqid() . "_" . basename($_FILES['profile_picture']['name']);
    $file_path = $upload_dir . $file_name;

    // Move the uploaded file to the server
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $file_path)) {
      // Update the user's profile picture in the database
      $update_picture_sql = "UPDATE users SET profile_picture='$file_path' WHERE id=$user_id";
      if ($conn->query($update_picture_sql) === TRUE) {
        echo "Profile picture updated successfully!";
      } else {
        echo "Error updating profile picture: " . $conn->error;
      }
    } else {
      echo "Error uploading file.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile</title>
</head>

<body>
  <h2>Edit Profile</h2>
  <form method="POST" action="profile.php" enctype="multipart/form-data">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>

    <label for="phone">Phone:</label><br>
    <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required><br><br>

    <label for="address">Address:</label><br>
    <textarea id="address" name="address" required><?php echo $user['address']; ?></textarea><br><br>

    <input type="submit" value="Update Profile">
    <!-- 
    <h3>Change Password</h3>
    <label for="current_password">Current Password:</label><br>
    <input type="password" id="current_password" name="current_password" required><br><br>

    <label for="new_password">New Password:</label><br>
    <input type="password" id="new_password" name="new_password" required><br><br>

    <label for="confirm_password">Confirm New Password:</label><br>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>

    <input type="submit" name="change_password" value="Change Password"><br><br> -->

    <h3>Upload Profile Picture</h3>
    <label for="profile_picture">Profile Picture:</label><br>
    <input type="file" id="profile_picture" name="profile_picture" accept="image/*"><br><br>

    <input type="submit" value="Upload Picture">
  </form>
  <br>
  <a href="dashboard.php">Back to Dashboard</a> | <a href="logout.php">Logout</a>
</body>

</html>