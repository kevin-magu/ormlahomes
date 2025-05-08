<?php
include './includes/connection.php';
header('Content-Type: application/json');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get raw input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$requiredFields = ['property_id', 'title', 'listingType', 'mainCategory', 'subcategory'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

$propertyId = (int) $data['property_id'];
$title = $conn->real_escape_string($data['title']);
$listingType = $conn->real_escape_string($data['listingType']);
$mainCategory = $conn->real_escape_string($data['mainCategory']);
$subcategory = $conn->real_escape_string($data['subcategory']);
$location = $conn->real_escape_string($data['location'] ?? '');
$mapLink = $conn->real_escape_string($data['mapLink'] ?? '');
$cost = $conn->real_escape_string($data['cost'] ?? '');
$rentPerMonth = $conn->real_escape_string($data['rentPerMonth'] ?? '');
$size = $conn->real_escape_string($data['propertySize'] ?? '');
$bedrooms = $conn->real_escape_string($data['bedrooms'] ?? '');
$bathrooms = $conn->real_escape_string($data['bathrooms'] ?? '');
$garages = $conn->real_escape_string($data['garages'] ?? '');
$yearBuilt = $conn->real_escape_string($data['yearBuilt'] ?? '');
$condition = $conn->real_escape_string($data['condition'] ?? '');
$floor = $conn->real_escape_string($data['floor'] ?? '');
$amenities = $conn->real_escape_string($data['amenities'] ?? '');
$nearby = $conn->real_escape_string($data['nearby'] ?? '');
$description = $conn->real_escape_string($data['propertyDescription'] ?? '');

// Update SQL
$updateQuery = "UPDATE properties SET 
    title = '$title',
    listing_type = '$listingType',
    broad_category = '$mainCategory',
    property_type = '$subcategory',
    location = '$location',
    map_link = '$mapLink',
    price = '$cost',
    rent_per_month = '$rentPerMonth',
    propertySize = '$size',
    bedrooms = '$bedrooms',
    bathrooms = '$bathrooms',
    garage = '$garages',
    yearBuilt = '$yearBuilt',
    property_condition = '$condition',
    floor = '$floor',
    amenities = '$amenities',
    other_property_amenities = '$nearby',
    description = '$description'
    WHERE id = $propertyId";

if (!$conn->query($updateQuery)) {
    echo json_encode(['success' => false, 'message' => 'Failed to update property.', 'error' => $conn->error]);
    exit;
}

// Handle images
if (!empty($data['images']) && is_array($data['images'])) {
    foreach ($data['images'] as $index => $base64Image) {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            echo json_encode(['success' => false, 'message' => "Image #$index has invalid format."]);
            exit;
        }

        $extension = strtolower($matches[1]);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowed)) {
            echo json_encode(['success' => false, 'message' => "Unsupported image type: .$extension"]);
            exit;
        }

        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Image));
        if ($imageData === false) {
            echo json_encode(['success' => false, 'message' => "Failed to decode image #$index"]);
            exit;
        }

        $fileName = uniqid('img_', true) . '.' . $extension;
        $filePath = "uploads/$fileName";

        if (!file_put_contents($filePath, $imageData)) {
            echo json_encode(['success' => false, 'message' => "Failed to save image #$index"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare image insert.', 'error' => $conn->error]);
            exit;
        }

        $stmt->bind_param("is", $propertyId, $filePath);
        if (!$stmt->execute()) {
            $stmt->close();
            echo json_encode(['success' => false, 'message' => 'Failed to insert image into database.']);
            exit;
        }

        $stmt->close();
    }
}

echo json_encode(['success' => true, 'message' => 'Property updated successfully.']);
$conn->close();
