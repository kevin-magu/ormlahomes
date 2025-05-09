<?php
// Suppress errors, log them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');
error_reporting(E_ALL);

// Start output buffering to prevent unintended output
ob_start();

require_once './includes/connection.php';

// Error logging function
function logError(string $message) {
    $logFile = 'error_log.txt';
    $logMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    if (!is_writable($logFile) && !is_writable(dirname($logFile))) {
        error_log('Cannot write to error_log.txt: ' . $message);
    } else {
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

// Allow empty strings, but ensure the field exists in the JSON payload
function validateRequiredFields(array $data, array $requiredFields): ?string {
    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $data)) {
            return "Missing field: $field";
        }
    }
    return null;
}


// Process base64-encoded images
function processImages(array $images, string $uploadDir, int $propertyId): array {
    if (!file_exists($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        throw new Exception('Failed to create upload directory');
    }
    if (!is_writable($uploadDir)) {
        throw new Exception('Upload directory is not writable');
    }

    $imagePaths = [];
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    foreach ($images as $index => $img) {
        if (!is_string($img) || !preg_match('/^data:image\/(\w+);base64,/', $img, $type)) {
            throw new Exception("Invalid image format at index $index");
        }

        $ext = strtolower($type[1]);
        if (!in_array($ext, $allowedTypes)) {
            throw new Exception("Unsupported image type: $ext at index $index");
        }

        $base64Data = substr($img, strpos($img, ',') + 1);
        $decoded = base64_decode($base64Data, true);
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

// Set JSON header
header('Content-Type: application/json');

try {
    // Validate session
    if (!session_start()) {
        logError('Session start failed');
        throw new Exception('Session initialization failed');
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }
    $userId = (int) $_SESSION['user_id'];

    // Validate database connection
    if (!$conn || $conn->connect_error) {
        logError('Database connection failed: ' . ($conn ? $conn->connect_error : 'No connection object'));
        throw new Exception('Database connection failed');
    }

    // Get and validate JSON input
    $rawInput = file_get_contents('php://input');
    if (empty($rawInput)) {
        logError('Empty request body received');
        throw new Exception('Empty request body');
    }

    $data = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        $errorMsg = json_last_error_msg();
        logError('Invalid JSON received: ' . $errorMsg . ' | Input: ' . $rawInput . ' | Content-Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));
        throw new Exception('Invalid JSON received: ' . $errorMsg);
    }

    // Define required fields
    $requiredFields = ['propertyId', 'listingType', 'mainCategory', 'subcategory', 'location', 'cost', 'title'];

    // Validate required fields
    if ($error = validateRequiredFields($data, $requiredFields)) {
        throw new Exception($error);
    }

    
    if (empty($data['propertyId']) || !is_numeric($data['propertyId']) || $data['propertyId'] <= 0) {
        throw new Exception('Invalid or missing propertyId');
    }

    // Prepare data
    $propertyId = (int) $data['propertyId'];
    $listingType = trim($data['listingType']);
    $mainCategory = trim($data['mainCategory']);
    $subcategory = trim($data['subcategory']);
    $location = trim($data['location']);
    $mapLink = trim($data['mapLink'] ?? '');
    $cost = trim($data['cost']);
    $garages = (int) ($data['garages'] ?? 0);
    $bedrooms = (int) ($data['bedrooms'] ?? 0);
    $bathrooms = (int) ($data['bathrooms'] ?? 0);
    $allAmenities = trim($data['amenities'] ?? '');
    $nearby = trim($data['nearby'] ?? '');
    $propertyDescription = trim($data['propertyDescription'] ?? '');
    $propertySize = trim($data['propertySize'] ?? '');
    $title = trim($data['title']);
    $propertyCondition = trim($data['propertyCondition'] ?? '');
    $floor = (int) ($data['floor'] ?? 0);
    $yearBuilt = (int) ($data['yearBuilt'] ?? 0);

    // Handle amenities based on category
    $residentialAmenity = '';
    $otherAmenity = '';
    if (strtolower($mainCategory) === 'residential') {
        $residentialAmenity = $allAmenities;
    } else {
        $otherAmenity = $allAmenities;
    }

    // Start transaction
    $conn->begin_transaction();

    // Update property
    $updateQuery = "UPDATE properties SET
        price = ?, location = ?, map_link = ?, bedrooms = ?, bathrooms = ?, garage = ?,
        amenities = ?, other_property_amenities = ?, accessibilities = ?, description = ?,
        propertySize = ?, property_type = ?, listing_type = ?, broad_category = ?,
        title = ?, property_condition = ?, floor = ?, yearBuilt = ?
        WHERE id = ? AND user_id = ?";

    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        logError('Prepare failed: ' . $conn->error);
        throw new Exception('Database prepare failed');
    }

    $stmt->bind_param(
        'ssssssssssssssssssii',
        $cost,
        $location,
        $mapLink,
        $bedrooms,
        $bathrooms,
        $garages,
        $residentialAmenity,
        $otherAmenity,
        $nearby,
        $propertyDescription,
        $propertySize,
        $subcategory,
        $listingType,
        $mainCategory,
        $title,
        $propertyCondition,
        $floor,
        $yearBuilt,
        $propertyId,
        $userId
    );

    if (!$stmt->execute()) {
        logError('Query execution failed: ' . $stmt->error . ' | Query: ' . $updateQuery);
        throw new Exception('Failed to update property');
    }
    $stmt->close();

    // Handle image uploads
    if (!empty($data['images']) && is_array($data['images'])) {
        $uploadDir = 'Uploads/';
        $imagePaths = processImages($data['images'], $uploadDir, $propertyId);

        $imgStmt = $conn->prepare('INSERT INTO property_images (property_id, image_url) VALUES (?, ?)');
        if (!$imgStmt) {
            logError('Image prepare failed: ' . $conn->error);
            throw new Exception('Image prepare failed');
        }

        foreach ($imagePaths as $path) {
            $imgStmt->bind_param('is', $propertyId, $path);
            if (!$imgStmt->execute()) {
                logError('Image insert failed: ' . $imgStmt->error);
                throw new Exception('Failed to save image to database');
            }
        }
        $imgStmt->close();
    }

    // Commit transaction
    $conn->commit();

    // Send success response
    ob_end_clean();
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Property updated successfully',
        'propertyId' => $propertyId
    ]);

} catch (Exception $e) {
    if ($conn && $conn->ping()) {
        $conn->rollback();
    }
    logError('Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'rawInput' => isset($rawInput) ? $rawInput : null,
        'contentType' => $_SERVER['CONTENT_TYPE'] ?? 'Not set'
    ]);
} finally {
    if ($conn && $conn->ping()) {
        $conn->close();
    }
}
?>