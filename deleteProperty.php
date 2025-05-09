<?php
require_once './includes/connection.php';
header('Content-Type: application/json');
session_start();

// Log errors
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');
error_reporting(E_ALL);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['property_id']) || !is_numeric($input['property_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or missing property ID']);
        exit;
    }

    $propertyId = (int)$input['property_id'];

    // Start transaction
    $conn->begin_transaction();

    // 1. Get image paths
    $stmt = $conn->prepare("SELECT image_url FROM property_images WHERE property_id = ?");
    $stmt->bind_param("i", $propertyId);
    $stmt->execute();
    $result = $stmt->get_result();

    $imagePaths = [];
    while ($row = $result->fetch_assoc()) {
        $imagePaths[] = $row['image_url'];
    }
    $stmt->close();

    // 2. Delete image records
    $stmt = $conn->prepare("DELETE FROM property_images WHERE property_id = ?");
    $stmt->bind_param("i", $propertyId);
    $stmt->execute();
    $stmt->close();

    // 3. Delete the property
    $stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
    $stmt->bind_param("i", $propertyId);
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete property: " . $stmt->error);
    }
    $stmt->close();

    // 4. Delete the actual image files
    foreach ($imagePaths as $path) {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Property deleted successfully.']);

} catch (Exception $e) {
    if ($conn && $conn->ping()) {
        $conn->rollback();
    }
    error_log("Delete property failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while deleting the property.']);
} finally {
    if ($conn && $conn->ping()) {
        $conn->close();
    }
}
