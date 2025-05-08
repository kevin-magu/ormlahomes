<?php
include './includes/connection.php'; // Database connection

header('Content-Type: application/json');

// Read incoming JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

// Fetch user by email
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
    exit;
}

// Generate a secure random token
$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+7 days')); // Token valid for 7 days

// Store token in database
$insertToken = $conn->prepare("INSERT INTO tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
$insertToken->bind_param("iss", $user['id'], $token, $expiresAt);

if ($insertToken->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Login successful.',
        'token' => $token // send token back to JS
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
}

$conn->close();
?>
