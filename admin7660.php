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

0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Orlma Homes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/admin.css">
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <div class="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Orlma Homes</h2>
            <p>Admin Dashboard</p>
            <button class="close-sidebar"><i class="fas fa-times"></i></button>
        </div>

        <div class="admin-profile">
            <div class="admin-avatar"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
            <div class="admin-name"><?= $adminName ?></div>
            <div class="admin-role">ADMINISTRATOR</div>
        </div>

        <nav class="sidebar-menu">
            <a href="admin7660.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="adminListings" class="menu-item">
                <i class="fas fa-home"></i>
                <span>Properties</span>
            </a>
            <a href="" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="sell.php" class="menu-item">
                <i class="fas fa-plus-circle"></i>
                <span>Add Listing</span>
            </a>
            <a href="./" class="menu-item">
                <i class="fas fa-globe"></i>
                <span>View Website</span>
            </a>
            <a href="" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <div class="page-title">
                    <h1>Dashboard Overview</h1>
                    <p>Welcome back, <?= $adminName ?></p>
                </div>
                <div class="header-actions">
                    <span class="last-updated">Last updated: <?= date('M j, Y g:i A') ?></span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Listings</h3>
                        <div class="card-icon">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                    <div class="card-value"><?= $totalListings ?></div>
                    <div class="card-footer">
                        <i class="fas fa-database"></i> All properties
                    </div>
                </div>


                <div class="stat-card">
                    <div class="card-header">
                        <h3 class="card-title">Under Review</h3>
                        <div class="card-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="card-value"><?= $listingsUnderReview ?></div>
                    <div class="card-footer">
                        <i class="fas fa-exclamation-circle"></i> Needs approval --test
                    </div>
                </div>

                <div class="stat-card">
                    <div class="card-header">
                        <h3 class="card-title">Total Users</h3>
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="card-value"><?= $totalUsers ?></div>
                    <div class="card-footer">
                        <i class="fas fa-user-friends"></i> Registered users
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h2 class="section-title">Quick Actions</h2>
            <div class="quick-actions">
                <a href="adminListings" class="action-card">
                    <i class="fas fa-list"></i>
                    <h3>Manage Listings</h3>
                    <p>View all properties</p>
                </a>
                <a href="sell.php" class="action-card">
                    <i class="fas fa-plus"></i>
                    <h3>Add Property</h3>
                    <p>Create new listing</p>
                </a>
                <a href="" class="action-card">
                    <i class="fas fa-user-cog"></i>
                    <h3>Manage Users</h3>
                    <p>View all users</p>
                </a>
                <a href="./" class="action-card">
                    <i class="fas fa-external-link-alt"></i>
                    <h3>View Website</h3>
                    <p>Go to main site</p>
                </a>
            </div>

            <!-- System Info -->
            <h2 class="section-title">System Information</h2>
            <div class="system-info">
                <div class="info-card">
                    <i class="fas fa-server"></i>
                    <div>
                        <h3>Database Status</h3>
                        <p>Connected to MySQL server</p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <h3>Current Date</h3>
                        <p><?= date('F j, Y') ?></p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-code-branch"></i>
                    <div>
                        <h3>System Version</h3>
                        <p>Orlma Homes v1.0 -Beta</p>
                    </div>
                </div>
            </div>
        </main>

<script>
// Toggle sidebar
document.querySelector('.menu-toggle').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.add('active');
});

// Close sidebar on close button
document.querySelector('.close-sidebar').addEventListener('click', function () {
    document.querySelector('.sidebar').classList.remove('active');
});

// Close sidebar when clicking outside
document.addEventListener('click', function (event) {
    const sidebar = document.querySelector('.sidebar');
    const menuToggle = document.querySelector('.menu-toggle');

    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
        sidebar.classList.remove('active');
    }
});

</script>
</body>
</html>