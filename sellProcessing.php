<?php
session_start();
require_once './includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ./sell");
    exit;
}

function logError(string $message) {
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

function validateRequiredFields(array $data, array $requiredFields): ?string {
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            return "Missing or empty field: $field";
        }
    }
    return null;
}

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

header('Content-Type: application/json');

try {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE || !$data) {
        throw new Exception('Invalid JSON received');
    }

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    $userId = (int) $_SESSION['user_id'];

    $requiredFields = [
        'listingType', 'mainCategory', 'subcategory', 'location', 'mapLink',
        'cost', 'mortgage', 'bedrooms', 'bathrooms', 'garages',
        'amenities', 'nearby', 'propertyDescription', 'propertySize'
    ];

    if ($error = validateRequiredFields($data, $requiredFields)) {
        throw new Exception($error);
    }

    // Sanitize input
    $listingType         = trim($data['listingType']);
    $mainCategory        = trim($data['mainCategory']);
    $subcategory         = trim($data['subcategory']);
    $location            = trim($data['location']);
    $mapLink             = trim($data['mapLink']);
    $cost                = trim($data['cost']);
    $mortgage            = trim($data['mortgage']);
    $garages             = (int) $data['garages'];
    $bedrooms            = (int) $data['bedrooms'];
    $bathrooms           = (int) $data['bathrooms'];
    $allAmenities        = trim($data['amenities']);
    $nearby              = trim($data['nearby']);
    $propertyDescription = trim($data['propertyDescription']);
    $propertySize        = trim($data['propertySize']);

    if (empty($data['images']) || !is_array($data['images'])) {
        throw new Exception('No images provided');
    }

    // Decide where amenities go
    $residentialAmenity = '';
    $otherAmenity       = '';

    if (strtolower($mainCategory) === 'residential') {
        $residentialAmenity = $allAmenities;
    } else {
        $otherAmenity = $allAmenities;
    }

    // Begin transaction
    $conn->begin_transaction();

    // Insert into properties
    $insertQuery = "INSERT INTO properties 
        (price, location, map_link, mortgage_rate, bedrooms, bathrooms, garage, 
         amenities, other_property_amenities, accessibilities, description, propertySize, 
         property_type, listing_type, broad_category, user_id, under_review, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'yes', NOW())";

    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);

    $stmt->bind_param(
        "ssssssssssssssss",
        $cost, $location, $mapLink, $mortgage, $bedrooms, $bathrooms, $garages,
        $residentialAmenity, $otherAmenity, $nearby, $propertyDescription, $propertySize,
        $subcategory, $listingType, $mainCategory, $userId
    );

    if (!$stmt->execute()) {
        throw new Exception('Execution failed: ' . $stmt->error);
    }

    $propertyId = $stmt->insert_id;
    $stmt->close();

    // Log under review
    $reviewStmt = $conn->prepare("INSERT INTO under_review (property_id, user_id) VALUES (?, ?)");
    if (!$reviewStmt) throw new Exception('Prepare failed (under_review): ' . $conn->error);
    $reviewStmt->bind_param("ii", $propertyId, $userId);
    $reviewStmt->execute();
    $reviewStmt->close();

    // Save images
    $uploadDir = "uploads/";
    $imagePaths = processImages($data['images'], $uploadDir);

    $imgStmt = $conn->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
    if (!$imgStmt) throw new Exception('Prepare failed (images): ' . $conn->error);

    foreach ($imagePaths as $path) {
        $imgStmt->bind_param("is", $propertyId, $path);
        if (!$imgStmt->execute()) {
            throw new Exception("Image insert error: " . $imgStmt->error);
        }
    }
    $imgStmt->close();

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Property added successfully. Admin will approve the property shortly',
        'propertyId' => $propertyId
    ]);

} catch (Exception $e) {
    $conn->rollback();
    logError($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
