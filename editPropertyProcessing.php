<?php
session_start();
require_once './includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
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

    // Define required fields for property update
    $requiredFields = [
        'property_id',
        'title',
        'listingType',
        'mainCategory',
        'subcategory',
        'location',
        'mapLink',
        'cost',
        'rentPerMonth',
        'propertySize',
        'bedrooms',
        'bathrooms',
        'garages',
        'yearBuilt',
        'condition',
        'floor',
        'amenities',
        'nearby',
        'propertyDescription'
    ];

    // Validate required fields
    if ($error = validateRequiredFields($data, $requiredFields)) {
        throw new Exception($error);
    }

    // Sanitize input
    $propertyId          = (int) $data['property_id'];
    $listingType         = trim($data['listingType']);
    $mainCategory        = trim($data['mainCategory']);
    $subcategory         = trim($data['subcategory']);
    $location            = trim($data['location']);
    $mapLink             = trim($data['mapLink']);
    $cost                = trim($data['cost']);
    $rentPerMonth        = trim($data['rentPerMonth']);
    $propertySize        = trim($data['propertySize']);
    $bedrooms            = (int) $data['bedrooms'];
    $bathrooms           = (int) $data['bathrooms'];
    $garages             = (int) $data['garages'];
    $yearBuilt           = (int) $data['yearBuilt'];
    $condition           = trim($data['condition']);
    $floor               = (int) $data['floor'];
    $amenities           = trim($data['amenities']);
    $nearby              = trim($data['nearby']);
    $propertyDescription = trim($data['propertyDescription']);

    $conn->begin_transaction();

    // Update property
    $updateQuery = "UPDATE properties SET 
        title = ?, listing_type = ?, broad_category = ?, property_type = ?, 
        location = ?, map_link = ?, price = ?, rent_per_month = ?, propertySize = ?, 
        bedrooms = ?, bathrooms = ?, garage = ?, yearBuilt = ?, property_condition = ?, 
        floor = ?, amenities = ?, other_property_amenities = ?, description = ?, updated_at = NOW()
        WHERE id = ?";

    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);

    $stmt->bind_param(
        "sssssssssssssssssi", 
        $title, $listingType, $mainCategory, $subcategory, 
        $location, $mapLink, $cost, $rentPerMonth, $propertySize, 
        $bedrooms, $bathrooms, $garages, $yearBuilt, $condition, 
        $floor, $amenities, $nearby, $propertyDescription, $propertyId
    );

    if (!$stmt->execute()) {
        throw new Exception('Execution failed: ' . $stmt->error);
    }

    $stmt->close();

    // Handle images
    if (!empty($data['images']) && is_array($data['images'])) {
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
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Property updated successfully'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    logError($e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
