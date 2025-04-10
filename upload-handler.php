<?php
include 'includes/connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if directory exists and is writable
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_writable($uploadDir)) {
    die("Upload directory is not writable!");
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Save property info
    if (!isset($_POST['price']) || !isset($_POST['description']) || !isset($_POST['propertyType']) || !isset($_POST['listingType'])) {
        throw new Exception("Missing property data!");
    }

    // Use htmlspecialchars instead of deprecated FILTER_SANITIZE_STRING
    $price = htmlspecialchars($_POST['price'], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
    $propertyType = htmlspecialchars($_POST['propertyType'], ENT_QUOTES, 'UTF-8');
    $listingType = htmlspecialchars($_POST['listingType'], ENT_QUOTES, 'UTF-8');

    // Prepare and execute the query to insert property info along with property type and listing type
    $stmt = $conn->prepare("INSERT INTO properties (price, description, property_type, listing_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss" , $price, $description, $propertyType, $listingType);
    $stmt->execute();
    $property_id = $conn->insert_id;

    // Upload and save images
    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        throw new Exception("No images uploaded!");
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
            $fileType = $_FILES['images']['type'][$key];
            $fileSize = $_FILES['images']['size'][$key];
            $originalName = $_FILES['images']['name'][$key];

            // Validate file
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Invalid file type for: $originalName");
            }
            if ($fileSize > $maxSize) {
                throw new Exception("File too large: $originalName");
            }

            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $uniqueName = uniqid('img_') . '.' . $ext;
            $uploadPath = $uploadDir . $uniqueName;

            if (!move_uploaded_file($tmp_name, $uploadPath)) {
                throw new Exception("Failed to upload image: $originalName");
            }

            $stmt = $conn->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
            $stmt->bind_param("is", $property_id, $uploadPath);
            $stmt->execute();
        }
    }

    $conn->commit();
    echo "<h3>Property uploaded successfully!</h3><p><a href='index.php'>Upload another</a></p>";

} catch (Exception $e) {
    $conn->rollback();
    echo "<h3>Error:</h3><p>" . $e->getMessage() . "</p>";
    echo "<p><a href='upload.php'>Try again</a></p>";
}

$conn->close();
?>
