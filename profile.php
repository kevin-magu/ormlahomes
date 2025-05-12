<?php 
include './includes/navbar.php'; 
require_once './includes/connection.php'; 

$username = 'Guest';
$propertyCount = 0;

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

    // Fetch property count
    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM properties WHERE user_id = ?");
    $stmt2->bind_param("i", $userId);
    $stmt2->execute();
    $stmt2->bind_result($count);
    if ($stmt2->fetch()) {
        $propertyCount = $count;
    }
    $stmt2->close();
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
    <title>Profile</title>
</head>  
<body>
<div id="userResponse" class="response-box hidden"></div>
    <div class="main-content-container">
        <p class="username justify-centre display-flex">Welcome, <?php echo $username; ?>. Manage your lisiting here. Total listings: <?php echo $propertyCount; ?></p>
        <p></p>

        <div class="listings property-cards-wrapper">
            <?php
            include 'includes/connection.php';
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

            function renderPropertyCard($property, $images, $isRental = false) {
                $token = base64_encode("property_" . $property['id']);
                ?>
                
                    <a href="property?ref=<?= urlencode($token) ?>">
                        <div class="property-card">
                            <i class="fa-regular fa-heart heart-icon"
                            data-property-id="<?php echo htmlspecialchars((int) $property['id'], ENT_QUOTES, 'UTF-8'); ?>"></i>

                            <?php $token = base64_encode("property_" . $property['id']); ?>
                            <?php renderImageSlider($images); ?>
                            <h3><?php echo htmlspecialchars($property['property_type'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <div class="listing-type"><?= htmlspecialchars($property['listing_type']) ?></div>
                            <?php if (!$isRental): ?>
                                <div class="display-flex homesize">
                                    <p class="card-square"></p>
                                    <p><?php echo htmlspecialchars($property['propertySize'], ENT_QUOTES, 'UTF-8'); ?> SQFT</p>
                                </div>
                            <?php endif; ?>
                            <p>Ksh <?php echo number_format((int)$property['price']) ?><?php echo $isRental ? ' /month' : ''; ?></p>
                            <p><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p>
                            <p>
                            <i class="fa-solid fa-pen-to-square editIcon" data-id="<?php echo $property['id']; ?>" title="Edit your listing"></i>
                            </p>



                            
                        </div>
                    </a>
                <?php
            }

            function renderUserProperties($conn, $userId) {
                $query = "SELECT * FROM properties WHERE user_id = ?";
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
                renderUserProperties($conn, $userId);
            }

            $conn->close();
            ?>
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
