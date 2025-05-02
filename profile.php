<?php 
session_start();
include './includes/navbar.php'; 
require_once './includes/connection.php'; 


$username = 'Guest';

if (isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($name);
    if ($stmt->fetch()) {
        $username = htmlspecialchars($name);
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css">
    <link rel="stylesheet" href="./styles/profile.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <div class="main-content-container margin-top50">
        <p class="username">Welcome <?php echo $username; ?></p>
    </div>
</body>
</html>
