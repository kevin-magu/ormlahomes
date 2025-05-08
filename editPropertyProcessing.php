<?php
include './includes/connection.php';
header('Content-Type: application/json');
session_start();

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['property_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input or missing property ID']);
    exit;
}

$propertyId = (int)$data['property_id'];

// Prepare all fields
$title = $conn->real_escape_string($data['title'] ?? '');
$listingType = $conn->real_escape_string($data['listingType'] ?? '');
$mainCategory = $conn->real_escape_string($data['mainCategory'] ?? '');
$subcategory = $conn->real_escape_string($data['subcategory'] ?? '');
$location = $conn->real_escape_string($data['location'] ?? '');
$mapLink = $conn->real_escape_string($data['mapLink'] ?? '');
$cost = $conn->real_escape_string($data['cost'] ?? '');
$rentPerMonth = $conn->real_escape_string($data['rentPerMonth'] ?? '');
$size = $conn->real_escape_string($data['propertySize'] ?? '');
$bedrooms = $conn->real_escape_string($data['bedrooms'] ?? '');
$bathrooms = $conn->real_escape_string($data['bathrooms'] ?? '');
$garages = $conn->real_escape_string($data['garages'] ?? '');
$yearBuilt = $conn->real_escape_string($data['yearBuilt'] ?? '');
$condition = $conn->real_escape_string($data['propertyCondition'] ?? '');
$floor = $conn->real_escape_string($data['floor'] ?? '');
$amenities = $conn->real_escape_string($data['amenities'] ?? '');
$nearby = $conn->real_escape_string($data['nearby'] ?? '');
$description = $conn->real_escape_string($data['propertyDescription'] ?? '');

// Update the property
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
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update property', 'error' => $conn->error]);
    exit;
}

// Handle base64 images if any
if (isset($data['images']) && is_array($data['images'])) {
    foreach ($data['images'] as $base64Image) {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = strtolower($matches[1]);
            $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64Image));
            $fileName = uniqid('img_', true) . '.' . $extension;
            $filePath = "uploads/$fileName";

            if (file_put_contents($filePath, $imageData)) {
                $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
                $stmt->bind_param("is", $propertyId, $filePath);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}else{
    echo json_encode(['success' => false, 'message' => 'handling images failed.']);
}

echo json_encode(['success' => true, 'message' => 'Property updated successfully.']);
$conn->close();
