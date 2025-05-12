<?php 
include './includes/navbar.php'; 
require_once './includes/connection.php'; 

$username = 'Guest';

if (isset($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];

    // Fetch username
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($name);
    if ($stmt->fetch()) {
        $username = htmlspecialchars($name);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css">
    <link rel="stylesheet" href="./styles/profile.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Properties</title>
</head>  
<body>
<div id="userResponse" class="response-box hidden"></div>
    <div class="main-content-container">
        <p class="all-pages-title justify-centre">Welcome, <?php echo $username; ?></p>
        <p class="all-pages-title justify-centre">This are favourite properties</p>

        <div class="property-cards-wrapper property-cards-container">
            <?php
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

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

            function renderPropertyCard($property, $images) {
                $token = base64_encode("property_" . $property['id']);
                ?>
                
                    <a href="property?ref=<?= urlencode($token) ?>">
                        <div class="property-card">
                            <i class="fa-regular fa-heart heart-icon"
                               data-property-id="<?php echo htmlspecialchars((int) $property['id'], ENT_QUOTES, 'UTF-8'); ?>"></i>

                            <?php renderImageSlider($images); ?>
                            <h3><?php echo htmlspecialchars($property['property_type'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <div class="listing-type"><?= htmlspecialchars($property['listing_type']) ?></div>
                            <div class="display-flex homesize">
                                <p class="card-square"></p>
                                <p><?php echo htmlspecialchars($property['propertySize'], ENT_QUOTES, 'UTF-8'); ?> SQFT</p>
                            </div>
                            <p>Ksh <?php echo number_format((int)$property['price']) ?></p>
                            <p><i class="fa-solid fa-location-dot"></i>
                                <?php echo htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </a>
                <?php
            }

            function renderFavoritedProperties($conn, $userId) {
                $query = "
                    SELECT p.* 
                    FROM properties p
                    INNER JOIN favorites f ON p.id = f.property_id
                    WHERE f.user_id = ?
                ";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();

                $imageQuery = "SELECT * FROM property_images WHERE property_id = ?";

                while ($property = $result->fetch_assoc()) {
                    $stmtImg = $conn->prepare($imageQuery);
                    $stmtImg->bind_param("i", $property['id']);
                    $stmtImg->execute();
                    $images = $stmtImg->get_result();
                    renderPropertyCard($property, $images);
                    $stmtImg->close();
                }

                $stmt->close();
            }

            if (isset($userId)) {
                renderFavoritedProperties($conn, $userId);
            } else {
                echo "<p>Please log in to view your favorite properties.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="./scripts/swiper.js"></script>
</body>
</html>
