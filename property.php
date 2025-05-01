<?php 
include './includes/connection.php';

// Step 1: Get `ref` from URL
if (!isset($_GET['ref'])) {
    echo "Invalid property reference.";
    exit;
}

// Step 2: Decode the Base64-encoded token
$decoded = base64_decode($_GET['ref']);

// Step 3: Extract the ID
if (strpos($decoded, 'property_') !== 0) {
    echo "Invalid token format.";
    exit;
}

$propertyId = intval(str_replace('property_', '', $decoded));

if ($propertyId <= 0) {
    echo "Invalid property ID.";
    exit;
}

// Fetch 7 images
$stmt = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY created_at ASC LIMIT 7");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();
$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row;
}
$stmt->close();

// Fetch all images for full gallery
$stmtAll = $conn->prepare("SELECT * FROM property_images WHERE property_id = ? ORDER BY created_at ASC");
$stmtAll->bind_param("i", $propertyId);
$stmtAll->execute();
$resultAll = $stmtAll->get_result();
$allImages = [];
while ($row = $resultAll->fetch_assoc()) {
    $allImages[] = $row;
}
$stmtAll->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./styles/commonStyles.css" />
    <link rel="stylesheet" href="./styles/property.css">
    <meta charset="UTF-8">
    <title>Property</title>
</head>
<body>
    <?php include './includes/navbar.php'; ?>

    <div class="main-content-container display-flex justify-centre">
    <div class="photo-grid">
    <div class="big-photo">
        <?php if (!empty($images[0])): ?>
        <img src="<?= htmlspecialchars($images[0]['image_url']) ?>" alt="Main Image">
        <?php endif; ?>
        </div>
        <div class="small-photos">
            <?php foreach (array_slice($images, 1, 2) as $img): ?>
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
