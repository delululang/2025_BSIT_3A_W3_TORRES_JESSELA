<?php
session_start();
require 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "Please log in first to manage your address.<br><a href='signin.php'>Go to Login</a>";
    exit();
}

$user = $_SESSION['user'];
$userId = $user['user_id'];

// Handle form submission to update address and phone
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';

    $updateSql = "UPDATE users SET address = ?, phone = ? WHERE user_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssi", $address, $phone, $userId);
    $stmt->execute();

    // Optionally refresh session data
    $_SESSION['user']['address'] = $address;
    $_SESSION['user']['phone'] = $phone;

    echo "<p style='color: green;'>Address updated successfully.</p>";
}

// Fetch updated user data
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Address</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Your Address Details</h2>

  <form method="POST" action="address.php">
    <p><strong>Name:</strong> <?= htmlspecialchars($userData['firstname'] . ' ' . $userData['lastname']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>

    <label for="address">Address:</label>
    <input type="text" name="address" id="address" value="<?= htmlspecialchars($userData['address']) ?>" required>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($userData['phone']) ?>" required>

    <button type="submit">Save Address</button>
  </form>

  <br>
  <a href="homepage.php">Back to Home</a>
</body>
</html>
