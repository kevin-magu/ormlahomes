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
</head>
<body>
    <?php include './includes/navbar.php'; ?>

    <div class="main-content-container display-flex justify-centre">
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
        </div>

        <button class="view-all-btn" onclick="showGallery()">View All Photos</button>

        <div class="full-gallery" id="fullGallery">
            <?php foreach ($allImages as $img): ?>
                <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Gallery Image">
            <?php endforeach; ?>
            <button class="cancel-btn" onclick="hideGallery()">Close</button>
        </div>
    </div>

    <script src="./scripts/property.js"></script>
</body>
</html>
