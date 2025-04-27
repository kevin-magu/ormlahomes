<?php
include './includes/connection.php'; // Database connection

header('Content-Type: application/json');

// Check if Authorization header exists
$headers = apache_request_headers();
$token = null;

if (isset($headers['Authorization'])) {
    // Extract the token from the Authorization header (Bearer <token>)
    $matches = [];
    preg_match('/Bearer (.+)/', $headers['Authorization'], $matches);
    $token = $matches[1] ?? null;
}

if (!$token) {
    echo json_encode(['success' => false, 'message' => 'Token not provided.']);
    exit();
}

// Query the database to verify token validity
$query = $conn->prepare("SELECT * FROM tokens WHERE token = ? AND expires_at > NOW()");
$query->bind_param("s", $token);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    // If no valid token is found
    echo json_encode(['success' => false, 'message' => 'Invalid or expired token.']);
} else {
    // Token is valid
    echo json_encode(['success' => true, 'message' => 'Token valid.']);
}

$conn->close();
?>
