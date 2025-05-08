<?php
include './includes/connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['image_id']) || empty($data['image_url'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Image ID and URL must not be empty.'
    ]);
    exit;
}

$imageId = (int)$data['image_id'];
$imageUrl = $data['image_url'];

// Attempt to delete image file from filesystem
$imagePath = __DIR__ . '/' . $imageUrl;
if (file_exists($imagePath)) {
    if (!unlink($imagePath)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete image file.'
        ]);
        exit;
    }
}

// Now delete from database
$stmt = $conn->prepare("DELETE FROM property_images WHERE id = ?");
$stmt->bind_param("i", $imageId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Image deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database deletion failed.'
    ]);
}

$stmt->close();
$conn->close();
?>
