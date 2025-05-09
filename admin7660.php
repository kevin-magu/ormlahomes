<?php
session_start();
include './includes/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

// Fetch user's name and category
$stmt = $conn->prepare("SELECT name, category FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $category);
$stmt->fetch();
$stmt->close();

if ($category !== 'ADMIN') {
    header('Location: login.php');
    exit;
}

$adminName = htmlspecialchars($name);
// Fetch total listings
$totalListingsResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM properties");
$totalListings = mysqli_fetch_assoc($totalListingsResult)['total'] ?? 0;

// Fetch listings under review
$underReviewResult = mysqli_query($conn, "SELECT COUNT(*) as total FROM under_review");
$listingsUnderReview = mysqli_fetch_assoc($underReviewResult)['total'] ?? 0;

// Fetch total users
$totalUsersResult = mysqli_query($conn, "SELECT COUNT(*) as users FROM users");
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['users'] ?? 0;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/commonStyles.css" />
    <link rel="stylesheet" href="./styles/dashboard.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
</head>
<body>
    <div class="dash-container display-flex">
        <h2 class="justify-centre margin-top30">ORLMA HOMES DASHBOARD</h2>
        <p class="justify-centre margin-top30">Welcome , <b>  Admin <?= $adminName ?></b></p>

        <div class="dash-cards-container display-flex justify-centre margin-top50">
            <a href="adminListings">
                <p class="card-description">Total no of listings</p>
                <p class="card-value"><?= $totalListings ?></p>
            </a>
            <a href="sell.php">
                <p class="card-description">Add a listing</p>
            </a>
            <a href="">
                <p class="card-description">Total no of users</p>
                <p class="card-value"><?= $totalUsers ?></p>
            </a>
        </div>
        <p class="dashboard-banner margin-top50">Orlma Homes and properties</p>
    </div>
</body>
</html>