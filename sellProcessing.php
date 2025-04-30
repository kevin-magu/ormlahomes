<?php
session_start();
require_once './includes/connection.php';

// Set content type to JSON
header('Content-Type: application/json');

// Standardized response function
function sendResponse(bool $success, string $message, array $data = [], int $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

// Log errors to a file for debugging
function logError(string $message) {
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

// Validate required fields
function validateRequiredFields(array $data, array $requiredFields): ?string {
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            return "Missing or empty field: $field";
        }
    }
    return null;
}

// Process and save images
function processImages(array $images, string $uploadDir): array {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imagePaths = [];
    foreach ($images as $index => $img) {
        if (!preg_match('/^data:image\/(\w+);base64,/', $img, $type)) {
            throw new Exception("Invalid image format at index $index");
        }

        $ext = strtolower($type[1]);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            throw new Exception("Unsupported image type: $ext at index $index");
        }

        $base64Data = substr($img, strpos($img, ',') + 1);
        $decoded = base64_decode($base64Data);
        if ($decoded === false) {
            throw new Exception("Image decoding failed at index $index");
        }

        $filename = uniqid('img_', true) . '.' . $ext;
        $filepath = $uploadDir . $filename;

        if (!file_put_contents($filepath, $decoded)) {
            throw new Exception("Failed to save image: $filename");
        }

        $imagePaths[] = $filepath;
    }

    return $imagePaths;
}

// Main processing logic
try {
    // Read and decode input
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE || !$data) {
        sendResponse(false, 'Invalid JSON received', [], 400);
    }

    // Check authentication
    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, 'User not authenticated', [], 401);
    }
    $userId = (int) $_SESSION['user_id'];

    // Define required fields
    $requiredFields = [
        'listingType', 'mainCategory', 'subcategory', 'location', 'mapLink',
        'cost', 'mortgage', 'bedrooms', 'bathrooms', 'garages','amenities', 'nearby',
        'propertyDescription', 'propertySize'
    ];

    // Validate required fields
    if ($error = validateRequiredFields($data, $requiredFields)) {
        sendResponse(false, $error, [], 400);
    }

    // Sanitize and extract values
    $listingType = trim($data['listingType']);
    $mainCategory = trim($data['mainCategory']);
    $subcategory = trim($data['subcategory']);
    $location = trim($data['location']);
    $mapLink = trim($data['mapLink']);
    $cost = trim($data['cost']);
    $mortgage = trim($data['mortgage']);
    $garages = (int) $data['garages'];
    $bedrooms = (int) $data['bedrooms'];
    $bathrooms = (int) $data['bathrooms'];
    $amenities = trim($data['amenities']);
    $nearby = trim($data['nearby']);
    $propertyDescription = trim($data['propertyDescription']);
    $propertySize = trim($data['propertySize']);

    // Validate images
    if (empty($data['images']) || !is_array($data['images'])) {
        sendResponse(false, 'No images provided', [], 400);
    }

    // Start transaction
    $conn->begin_transaction();

    // Insert into properties table
    $insertQuery = "INSERT INTO properties 
        (price, location, map_link, mortgage_rate, bedrooms, bathrooms, garage,amenities, 
         accessibilities, description, propertySize, property_type, listing_type, 
         broad_category, user_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,NOW())";

    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed for properties: ' . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssssss",
        $cost, $location, $mapLink, $mortgage, $bedrooms, $bathrooms,$garages,
        $amenities, $nearby, $propertyDescription, $propertySize,
        $subcategory, $listingType, $mainCategory, $userId
    );

    if (!$stmt->execute()) {
        throw new Exception('Execution failed for properties: ' . $stmt->error);
    }

    $propertyId = $stmt->insert_id;
    $stmt->close();

    //insert to under review 
$reviewStmt = $conn->prepare("INSERT INTO under_review (property_id, user_id) VALUES (?, ?)");
$reviewStmt->bind_param("ii", $propertyId, $userId);
$reviewStmt->execute();
$reviewStmt->close();


    // Process and save images
    $uploadDir = "uploads/";
    $imagePaths = processImages($data['images'], $uploadDir);

    // Insert images into property_images table
    $imgStmt = $conn->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
    if (!$imgStmt) {
        throw new Exception('Prepare failed for property_images: ' . $conn->error);
    }

    foreach ($imagePaths as $path) {
        $imgStmt->bind_param("is", $propertyId, $path);
        if (!$imgStmt->execute()) {
            throw new Exception("Error saving image $path: " . $imgStmt->error);
        }
    }
    $imgStmt->close();

    // Commit transaction
    $conn->commit();

    // Send success response
    sendResponse(true, 'Property listed successfully', [
        'propertyId' => $propertyId,
        'images' => $imagePaths
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollback();
    }

    // Log the error
    logError($e->getMessage());

    // Send error response
    sendResponse(false, 'Failed to process listing: ' . $e->getMessage(), [], 500);
} finally {
    // Close database connection
    $conn->close();
}
?>