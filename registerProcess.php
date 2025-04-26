<?php
include './includes/connection.php'; // Your database connection file

header('Content-Type: application/json');

// Read the raw JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

// Get data directly without trim (since you mentioned to match the new JS)
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$mobile = $data['mobile'] ?? '';
$password = $data['password'] ?? '';
$confirmPassword = $data['confirmPassword'] ?? '';

// Basic validation
if (empty($name) || empty($email) || empty($mobile) || empty($password) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
    exit;
}

// Check if email already exists
$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult && $checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
    exit;
}

// All good, hash password and insert user
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$insertStmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
$insertStmt->bind_param("ssss", $name, $email, $mobile, $hashedPassword);

if ($insertStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}

$conn->close();
?>
