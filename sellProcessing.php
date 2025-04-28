<?php
session_start();
header('Content-Type: application/json'); // Always send JSON

include './includes/connection.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$userId = (int) $_SESSION['user_id'];

// Collect form data safely
$listingType = trim($_POST['listingType'] ?? '');
$mainCategory = trim($_POST['mainCategory'] ?? '');
$subcategory = trim($_POST['subcategory'] ?? '');
$location = trim($_POST['location'] ?? '');
$mapLink = trim($_POST['mapLink'] ?? '');
$cost = trim($_POST['cost'] ?? '');
$mortgage = trim($_POST['mortgage'] ?? '');
$bedrooms = (int) ($_POST['bedrooms'] ?? 0);
$bathrooms = (int) ($_POST['bathrooms'] ?? 0);
$amenities = trim($_POST['amenities'] ?? '');
$nearby = trim($_POST['nearby'] ?? '');
$propertyDescription = trim($_POST['propertyDescription'] ?? '');
$propertySize = trim($_POST['propertySize'] ?? '');

// Handle images upload
$imagePaths = [];
if (isset($_FILES['images'])) {
    $uploadDirectory = "uploads/";

    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
        $imageName = basename($_FILES['images']['name'][$key]);
        $imageType = $_FILES['images']['type'][$key];
        $imageSize = $_FILES['images']['size'][$key];

        if (!in_array($imageType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid image type. Allowed: JPEG, PNG, GIF.']);
            exit();
        }

        if ($imageSize > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'Image size exceeds the 5MB limit.']);
            exit();
        }

        $extension = pathinfo($imageName, PATHINFO_EXTENSION);
        $uniqueImageName = uniqid('img_', true) . '.' . strtolower($extension);
        $imagePath = $uploadDirectory . $uniqueImageName;

        if (move_uploaded_file($tmpName, $imagePath)) {
            $imagePaths[] = $imagePath;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
            exit();
        }
    }
}

// Insert property details
$query = "INSERT INTO properties 
    (price, location, map_link, mortgage_rate, bedrooms, bathrooms, amenities, nearby, description, property_size, property_type, listing_type, broad_category, user_id, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param(
    "sssssssssssss",
    $cost,
    $location,
    $mapLink,
    $mortgage,
    $bedrooms,
    $bathrooms,
    $amenities,
    $nearby,
    $propertyDescription,
    $propertySize,
    $mainCategory,
    $listingType,
    $subcategory,
    $userId
);

if ($stmt->execute()) {
    $propertyId = $stmt->insert_id;

    // Insert images
    foreach ($imagePaths as $imagePath) {
        $imageQuery = "INSERT INTO property_images (property_id, image_url) VALUES (?, ?)";
        $imageStmt = $conn->prepare($imageQuery);
        if ($imageStmt) {
            $imageStmt->bind_param("is", $propertyId, $imagePath);
            $imageStmt->execute();
            $imageStmt->close();
        }
    }

    echo json_encode(['success' => true, 'message' => 'Property successfully listed.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error listing property: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
