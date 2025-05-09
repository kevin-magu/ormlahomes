<?php
session_start();
require_once './includes/connection.php';

// Verify admin access
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, category FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($adminName, $category);
$stmt->fetch();
$stmt->close();

if ($category !== 'ADMIN') {
    header('Location: login.php');
    exit;
}

function renderImageSlider($images) {
    ?>
    <section class="justify-centre">
        <div class="swiper mySwiper" style="position: relative;">
        <div class="swiper-wrapper">
            <?php while ($image = $images->fetch_assoc()): ?>
                <div class="swiper-slide">
                    <div class="image-slide"
                        style="background-image: url('<?php echo htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8'); ?>');">
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="swiper-button-prev next-buttons"></div>
        <div class="swiper-button-next next-buttons"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>
    <?php
}

function renderPropertyCard($property, $conn) {
    $propertyId = $property['id'];
    $token = base64_encode("property_" . $propertyId);

    $imageQuery = $conn->prepare("SELECT * FROM property_images WHERE property_id = ?");
    $imageQuery->bind_param("i", $propertyId);
    $imageQuery->execute();
    $images = $imageQuery->get_result();
    ?>
    <div class="cards-container">
    <a href="property?ref=<?= urlencode($token) ?>">
        <div class="property-card">
            <i class="fa-regular fa-heart heart-icon" data-property-id="<?= (int)$propertyId ?>"></i>
            <?php renderImageSlider($images); ?>
            <h3><?= htmlspecialchars($property['property_type']) ?></h3>
            <div class="listing-type"><?= htmlspecialchars($property['listing_type']) ?></div>
            <div class="display-flex homesize">
                <p class="card-square"></p>
                <p><?= htmlspecialchars($property['propertySize']) ?> SQFT</p>
            </div>
            <p>Ksh <?= number_format((int)$property['price']) ?><?= $property['listing_type'] === 'Rental' ? ' /month' : '' ?></p>
            <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($property['location']) ?></p>
            <p>
                <i class="fa-solid fa-pen-to-square editIcon" data-id="<?= $propertyId ?>" title="Edit this listing"></i>
            </p>
        </div>
    </a>
    </div>
    <?php
    $imageQuery->close();
}

// Fetch all properties
$propertyQuery = $conn->query("SELECT * FROM properties");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css">
    <link rel="stylesheet" href="./styles/profile.css">
    <link rel="stylesheet" href="./styles/dashboard.css">
    <script src="https://kit.fontawesome.com/e4c074505f.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Listings</title>
</head>
<body>
<div class="quick-links margin-top50 display-flex" style="margin-left: 20px;">
            <a href="./admin7660">Dashboard home</a>
            <a href="adminListings">Admin Listings</a>
</div>

<div id="userResponse" class="response-box hidden"></div>

<div class="main-content-container margin-top50">
    <p class="username">Welcome, Admin <?= htmlspecialchars($adminName) ?></p>
    <p>ALL LISTINGS ON PLATFORM</p>
    <p>TOTAL: <?= $propertyQuery->num_rows ?></p>

    <div class="listings property-cards-container">
        <?php while ($property = $propertyQuery->fetch_assoc()) {
            renderPropertyCard($property, $conn);
        } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="./scripts/swiper.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.editIcon').forEach(function (icon) {
        icon.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const propertyId = this.dataset.id;
            if (propertyId) {
                window.location.href = `editProperty.php?id=${propertyId}`;
            }
        });
    });
});
</script>
</body>
</html>
