<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include './includes/connection.php'; // replace with actual file

// Sanitize input
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$rating = (int)($_POST['rating'] ?? 0);
$feedbackText = htmlspecialchars(trim($_POST['feedbackText'] ?? ''));

// Basic validation
if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || $rating < 1 || $rating > 5 || !$feedbackText) {
    http_response_code(400);
    echo "Invalid input. Please fill out all fields correctly.";
    exit;
}

// Secure insert using prepared statement
$stmt = $conn->prepare("INSERT INTO feeback (name, email, text, rating) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $name, $email, $feedbackText, $rating);

if ($stmt->execute()) {
    echo "Thank you for your feedback!";
} else {
    http_response_code(500);
    echo "Failed to submit feedback.";
}

$stmt->close();
$conn->close();
?>
