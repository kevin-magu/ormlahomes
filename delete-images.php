<?php
include './includes/connection.php';
header('Content-Type: application/json');

// Get the raw JSON input
$data = json_decode(file_get_contents("php://input"), true);
$imageId = isset($data['image_id']) ? (int)$data['image_id'] : 0;

if ($imageId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid image ID.']);
    exit;
}

// 1. Retrieve the image URL from the database
$query = "SELECT image_url FROM property_images WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $imageId);
$stmt->execute();
$result = $stmt->get_result();
$image = $result->fetch_assoc();
$stmt->close();

if (!$image) {
    echo json_encode(['success' => false, 'message' => 'Image not found.']);
    exit;
}

$imagePath = $image['image_url'];  // e.g., "uploads/img_xxxx.jpg"
$fullPath = __DIR__ . '/' . $imagePath; // Absolute path to the file

// 2. Delete the record from the database
$deleteQuery = "DELETE FROM property_images WHERE id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("i", $imageId);
$deleteSuccess = $deleteStmt->execute();
$deleteStmt->close();

// 3. Delete the image file from the file system if it exists
if ($deleteSuccess) {
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }

    echo json_encode(['success' => true, 'message' => 'Image deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete image from database.']);
}
