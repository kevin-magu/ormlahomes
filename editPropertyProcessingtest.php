<?php
// Include database connection
include './includes/connection.php';

// Read the JSON input from the POST request
$data = json_decode(file_get_contents('php://input'), true);

// Function to sanitize and validate input data
function sanitizeInput($data, $conn) {
    return $conn->real_escape_string(trim($data));
}

// Validate that required fields exist
$requiredFields = ['property_id', 'title', 'listingType', 'price', 'location', 'bedrooms', 'bathrooms', 'propertySize'];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Sanitize data
$propertyId = sanitizeInput($data['property_id'], $conn);
$title = sanitizeInput($data['title'], $conn);
$listingType = sanitizeInput($data['listingType'], $conn);
$price = sanitizeInput($data['price'], $conn);
$location = sanitizeInput($data['location'], $conn);
$mapLink = isset($data['mapLink']) ? sanitizeInput($data['mapLink'], $conn) : '';
$bedrooms = sanitizeInput($data['bedrooms'], $conn);
$bathrooms = sanitizeInput($data['bathrooms'], $conn);
$garages = isset($data['garages']) ? sanitizeInput($data['garages'], $conn) : '';
$amenities = isset($data['amenities']) ? sanitizeInput($data['amenities'], $conn) : '';
$propertySize = sanitizeInput($data['propertySize'], $conn);
$yearBuilt = isset($data['yearBuilt']) ? sanitizeInput($data['yearBuilt'], $conn) : '';
$condition = isset($data['condition']) ? sanitizeInput($data['condition'], $conn) : '';
$floor = isset($data['floor']) ? sanitizeInput($data['floor'], $conn) : '';
$propertyDescription = isset($data['propertyDescription']) ? sanitizeInput($data['propertyDescription'], $conn) : '';
$images = isset($data['images']) ? $data['images'] : [];

// SQL query to update property details
$query = "UPDATE properties SET 
            title = '$title',
            listing_type = '$listingType',
            price = '$price',
            location = '$location',
            map_link = '$mapLink',
            bedrooms = '$bedrooms',
            bathrooms = '$bathrooms',
            garage = '$garages',
            amenities = '$amenities',
            propertySize = '$propertySize',
            yearBuilt = '$yearBuilt',
            property_condition = '$condition',
            floor = '$floor',
            description = '$propertyDescription'
          WHERE id = '$propertyId'";

// Execute the update query
if ($conn->query($query)) {
    // Process images if provided
    if (!empty($images)) {
        $imageInsertQuery = "DELETE FROM property_images WHERE property_id = '$propertyId'";
        $conn->query($imageInsertQuery); // Clear existing images for the property
        
        // Insert new images into the database
        foreach ($images as $imageBase64) {
            $imageData = base64_decode($imageBase64);
            $imagePath = './uploads/' . uniqid('img_', true) . '.jpg';
            file_put_contents($imagePath, $imageData);
            
            // Insert the image path into the database
            $insertImageQuery = "INSERT INTO property_images (property_id, image_url) VALUES ('$propertyId', '$imagePath')";
            $conn->query($insertImageQuery);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Property updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update property']);
}
?>
