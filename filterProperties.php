<?php
// Set content type early
header("Content-Type: text/html");

// Block direct access unless it's a POST request with JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || strpos($_SERVER["CONTENT_TYPE"], 'application/json') === false) {
    http_response_code(403);
    echo "Access Denied.";
    exit;
}

include './includes/connection.php';

// Read and decode incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);
$type = $data['type'] ?? '';
$price = $data['price'] ?? '';
$location = $data['location'] ?? '';


// Function to render a single property card with Swiper and link
function renderPropertyCard($property, $images) {
    $token = base64_encode("property_" . $property['id']);
    ?>
    <div class="cards-container">
        <a href="property?ref=<?= urlencode($token) ?>">
            <div class="property-card">
                <section class="justify-centre">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <?php while ($image = $images->fetch_assoc()): ?>
                                <div class="swiper-slide">
                                    <div class="image-slide"
                                         style="background-image: url('<?= htmlspecialchars($image['image_url'], ENT_QUOTES, 'UTF-8'); ?>');">
                                        <i class="fa-solid fa-heart"></i>
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
                <div class="display-flex homesize">
                    <p class="card-square"></p>
                    <p><?= htmlspecialchars($property['homeSize'], ENT_QUOTES, 'UTF-8'); ?> SQFT</p>
                </div>
                <p>Ksh <?= number_format((int)$property['price']); ?></p>
                <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($property['location'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </a>
    </div>
    <?php
}


// Base query
$sql = "SELECT * FROM properties WHERE listing_type = 'sale'";
$params = [];
$types = "";

// Filter by type
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

// Filter by price
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


$imageQuery = "SELECT * FROM property_images WHERE property_id = ?";

// Render results
if ($result->num_rows > 0) {
    while ($property = $result->fetch_assoc()) {
        $imgStmt = $conn->prepare($imageQuery);
        $imgStmt->bind_param("i", $property['id']);
        $imgStmt->execute();
        $images = $imgStmt->get_result();
        renderPropertyCard($property, $images);
    }
} else {
    echo '<p class="no-results-message" style="text-align:center; font-size:18px; margin-top:40px;">No results found</p>';
}

$conn->close();
?>
