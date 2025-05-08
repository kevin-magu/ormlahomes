<?php
header("Content-Type: text/html");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || strpos($_SERVER["CONTENT_TYPE"], 'application/json') === false) {
    http_response_code(403);
    echo "Access Denied.";
    exit;
}

include './includes/connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$type = $data['type'] ?? '';
$price = $data['price'] ?? '';
$location = $data['location'] ?? '';
$listingType = $data['listingType'] ?? '';

// Function to render property cards
function renderPropertyCard($property, $images) {
    $token = base64_encode("property_" . $property['id']);
    
    // Check if the property is favorited by the user (if authenticated)
    $isFavorited = false;
    if (isset($_SESSION['user_id'])) {
        global $conn;
        $stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $property['id']);
        $stmt->execute();
        $isFavorited = $stmt->get_result()->num_rows > 0;
        $stmt->close();
    }
    ?>
    <div class="cards-container">
        <div class="property-card" style="position: relative;">
            <!-- Heart icon outside the <a> tag with positioning -->
            <i class="fa-regular fa-heart heart-icon"
                   data-property-id="<?php echo htmlspecialchars((int) $property['id'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                
                <?php $token = base64_encode("property_" . $property['id']); ?>
            <a href="property?ref=<?= urlencode($token) ?>" style="text-decoration: none; color: inherit;">
                <section class="justify-centre">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <?php while ($image = $images->fetch_assoc()): ?>
                                <div class="swiper-slide">
                                    <div class="image-slide"
                                         style="background-image: url('<?= htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8'); ?>');">
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="swiper-button-prev next-buttons"></div>
                        <div class="swiper-button-next next-buttons"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </section>
                <h3><?= htmlspecialchars($property['property_type'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="listing-type"><?= htmlspecialchars($property['listing_type'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="display-flex homesize">
                    <p class="card-square"></p>
                    <p><?= htmlspecialchars($property['propertySize'], ENT_QUOTES, 'UTF-8'); ?> SQFT</p>
                </div>
                <p>Ksh <?= number_format((int)$property['price']); ?></p>
                <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8'); ?></p>
            </a>
        </div>
    </div>
    <?php
}

// Base query with hardcoded broad_category = 'commercial'
$sql = "SELECT * FROM properties WHERE broad_category = 'commercial'";
$params = [];
$types = "";

// Filter by property type
if ($type && strtolower($type) !== 'all') {
    $sql .= " AND property_type = ?";
    $params[] = $type;
    $types .= "s";
}

// Filter by location
if ($location && strtolower($location) !== 'all') {
    $sql .= " AND location = ?";
    $params[] = $location;
    $types .= "s";
}

// Filter by listing type
if ($listingType && strtolower($listingType) !== 'all') {
    $sql .= " AND listing_type = ?";
    $params[] = $listingType;
    $types .= "s";
}

// Filter by price range
if ($price) {
    switch ($price) {
        case 'below1m':
            $sql .= " AND price < 1000000";
            break;
        case '1m-5m':
            $sql .= " AND price BETWEEN 1000000 AND 5000000";
            break;
        case '5m-10m':
            $sql .= " AND price BETWEEN 5000000 AND 10000000";
            break;
        case '10m-15m':
            $sql .= " AND price BETWEEN 10000000 AND 15000000";
            break;
        case '15m-20m':
            $sql .= " AND price BETWEEN 15000000 AND 20000000";
            break;
        case 'above':
            $sql .= " AND price > 20000000";
            break;
    }
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Render each property
$imageQuery = "SELECT * FROM property_images WHERE property_id = ?";
if ($result->num_rows > 0) {
    while ($property = $result->fetch_assoc()) {
        $imgStmt = $conn->prepare($imageQuery);
        $imgStmt->bind_param("i", $property['id']);
        $imgStmt->execute();
        $images = $imgStmt->get_result();
        renderPropertyCard($property, $images);
    }
} else {
    echo '<p class="no-results-message" style="text-align:center; font-size:18px; margin-top:40px;">No listings found :(</p>';
}

$conn->close();
?>