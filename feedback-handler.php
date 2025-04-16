<?php
include './includes/connection.php';

echo "<pre>";
print_r($_POST);
echo "</pre>";

// Get form data with defaults
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null; // Use null instead of 0
$feedbackText = isset($_POST['feedbackText']) ? trim($_POST['feedbackText']) : '';

// Allow empty name since it's not required in the form
if (empty($email) || !isset($_POST['rating']) || $rating < 1 || $rating > 5 || empty($feedbackText)) {
    echo "Please fill all required fields.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
}

// Prepare and execute SQL query
try {
    $stmt = $conn->prepare("INSERT INTO feedback (name, email, rating, feedback_text) VALUES (:name, :email, :rating, :feedback_text)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':feedback_text', $feedbackText);
    $stmt->execute();
    echo "Feedback submitted successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>