<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to plain text for clear output
header('Content-Type: text/plain');

// Start session if required
session_start();

echo "========== PHP DEBUG TEST PAGE ==========\n\n";

// Read the raw input sent via fetch (JSON)
$rawInput = file_get_contents('php://input');

// Show the raw input
echo "----- Raw JSON Input -----\n";
if (empty($rawInput)) {
    echo "[EMPTY] No input received.\n\n";
} else {
    echo $rawInput . "\n\n";
}

// Try to decode JSON
$data = json_decode($rawInput, true);

// Show JSON decoding result
echo "----- JSON Decode Status -----\n";
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Error: " . json_last_error_msg() . "\n\n";
    exit;
} else {
    echo "JSON decoded successfully.\n\n";
}

// Show the resulting PHP array
echo "----- Decoded PHP Array -----\n";
print_r($data);

// Optional: Access individual fields
echo "\n----- Selected Fields (if exist) -----\n";
$fields = [
    'property_id', 'title', 'listingType', 'mainCategory', 'subcategory',
    'location', 'mapLink', 'cost', 'rentPerMonth', 'propertySize',
    'bedrooms', 'bathrooms', 'garages', 'yearBuilt', 'condition',
    'floor', 'amenities', 'nearby', 'propertyDescription'
];

foreach ($fields as $field) {
    if (array_key_exists($field, $data)) {
        echo "$field: " . $data[$field] . "\n";
    } else {
        echo "$field: [MISSING]\n";
    }
}

echo "\n========== END OF DEBUG ==========\n";
?>
