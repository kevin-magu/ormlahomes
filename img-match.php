<?php
require_once './includes/connection.php';

$uploadDir = './uploads/';
$uploadFiles = array_diff(scandir($uploadDir), ['.', '..']);

// Normalize to 'uploads/filename.ext' format
$uploadPaths = array_map(fn($f) => 'uploads/' . $f, $uploadFiles);

// Fetch image_url entries from the database
$query = "SELECT image_url FROM property_images";
$result = $conn->query($query);

if (!$result) {
    die("DB query failed: " . $conn->error);
}

$dbImageUrls = [];
while ($row = $result->fetch_assoc()) {
    $dbImageUrls[] = $row['image_url'];
}

// Find unmatched files
$unmatched = array_filter($uploadPaths, fn($file) => !in_array($file, $dbImageUrls));

// Delete unmatched files
$deletedCount = 0;
foreach ($unmatched as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Deleted: " . basename($file) . "\n";
            $deletedCount++;
        } else {
            echo "Failed to delete: " . basename($file) . "\n";
        }
    }
}

echo "\nTotal deleted: $deletedCount\n";

$conn->close();
