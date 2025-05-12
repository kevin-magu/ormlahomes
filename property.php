<?php 
include './includes/connection.php';

// Get and decode property ID
function getValidPropertyId($ref) {
    $decoded = base64_decode($ref, true);
    if (!$decoded || strpos($decoded, 'property_') !== 0) {
        return false;
    }

    $id = intval(str_replace('property_', '', $decoded));
    return $id > 0 ? $id : false;
}

$propertyId = isset($_GET['ref']) ? getValidPropertyId($_GET['ref']) : false;

if (!$propertyId) {
    echo "Invalid property reference.";
    exit;
}

// Fetch basic property details
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();
$propertyDetails = $result->fetch_assoc();
$stmt->close();


// Fetch all images once
$stmt = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();
$allImages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Separate first 3 images for preview
$previewImages = array_slice($allImages, 0, 3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property</title>
    <link rel="stylesheet" href="./styles/commonStyles.css" />
    <link rel="stylesheet" href="./styles/property.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php include './includes/navbar.php'; ?>

    <div class="main-content-container display-flex justify-centre">
        <div class="house-properties-container">
        <div class="property-feature all-pages-title">
            <p><?= htmlspecialchars($propertyDetails['property_type'] ?? 'N/A') ?></p>,
            <p><?= htmlspecialchars($propertyDetails['listing_type'] ?? 'N/A') ?></p> 
        </div>

        <div class="photo-grid">
        
            <div class="big-photo">
                <?php if (!empty($previewImages[0])): ?>
                    <img src="<?= htmlspecialchars($previewImages[0]['image_url']) ?>" alt="Main Image">
                <?php endif; ?>
            </div>
            <div class="small-photos">
                <?php foreach (array_slice($previewImages, 1) as $img): ?>
                    <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Sub Image">
                <?php endforeach; ?>
            </div>
            <button class="view-all-btn" onclick="showGallery()">View All Photos</button>
        </div>

        

        <div class="full-gallery" id="fullGallery">
            <?php foreach ($allImages as $img): ?>
                <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Gallery Image">
            <?php endforeach; ?>
            <button class="cancel-btn" onclick="hideGallery()">Close</button>
        </div>

        <div class="property-features">
            <?php
                $listingType = $propertyDetails['listing_type'];
                $priceText = '';
                if ($listingType === 'Rental') {
                    $priceText = 'Ksh ' . number_format($propertyDetails['rent_per_month']) . ' / month';
                } elseif ($listingType === 'For sale') {
                    $priceText = 'Ksh ' . number_format($propertyDetails['price']);
            }
            ?>
            <!-- Display it in HTML -->
            <p class="property-price"><?= $priceText ?></p>

        </div>

        <div class="price-features">
            
            <p> <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($propertyDetails['location'] ?? 'N/A') ?></p>  
        </div>

        

        <?php if (strtolower($propertyDetails['broad_category']) === 'residential'): ?>
            <div class="property-features amenities">
                <div class="property-feature">
                    <i class="fa-solid fa-bed"></i>
                    <p>Bedrooms</p>
                    <p><?= htmlspecialchars($propertyDetails['bedrooms'] ?? '__') ?></p>
                </div>
                <div class="property-feature">
                    <i class="fa-solid fa-bath"></i>
                    <p>Bathrooms</p>
                    <p><?= htmlspecialchars($propertyDetails['bathrooms'] ?? '__') ?></p>                
                </div>
                <div class="property-feature">
                    <i class="fa-solid fa-warehouse"></i>
                    <p>Garages</p>
                    <p><?= htmlspecialchars($propertyDetails['garage'] ?? '__') ?></p>
                </div>
            </div>
            <?php else: ?>
                <div class="property-features amenities">
                    <p><strong>Amenities:</strong> <?= htmlspecialchars($propertyDetails['other_property_amenities'] ?? '__') ?></p>
                </div>
            <?php endif; ?>


        <div class="property-features">
            <p><strong>Nearby Essentials:</strong> <?= htmlspecialchars($propertyDetails['accessibilities'] ?? '__') ?></p>
        </div>
        <div class="property-features">
            <p class="property-description"><?= htmlspecialchars($propertyDetails['description'] ?? '__') ?></p>
        </div>
        <div class="">
            <button>Tour in person</button>
            <button>Email / Call</button>
        </div>
        </div>
    </div>


<?php include './includes/footer.php' ?>
<script src="./scripts/property.js"></script>
</body>
</html>
