<?php
// Include the database connection file
include('./includes/connection.php');

// Start session to access $_SESSION
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ./login');
    exit;
}

$userId = $_SESSION['user_id']; // Safe to access now

// Handle GET request: Check if property is favorited
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['property_id'])) {
        $propertyId = intval($_GET['property_id']);

        // Check if the property is favorited by the user
        $stmt = $conn->prepare('SELECT * FROM favorites WHERE user_id = ? AND property_id = ?');
        $stmt->execute([$userId, $propertyId]);
        $favorite = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return whether it's favorited or not
        echo json_encode(['favorited' => $favorite ? true : false]);
    } else {
        echo json_encode(['error' => 'Property ID is required']);
    }
    exit;
}

// Handle POST request: Add or remove favorite
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['property_id'], $data['action'])) {
        $propertyId = intval($data['property_id']);
        $action = $data['action'];

        if ($action === 'add') {
            $stmt = $conn->prepare('INSERT INTO favorites (user_id, property_id) VALUES (?, ?)');
            $stmt->execute([$userId, $propertyId]);
            echo json_encode(['favorited' => true]);
        } elseif ($action === 'remove') {
            $stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ? AND property_id = ?');
            $stmt->execute([$userId, $propertyId]);
            echo json_encode(['favorited' => false]);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        echo json_encode(['error' => 'Property ID and action are required']);
    }
    exit;
}

// Default response for unsupported methods
echo json_encode(['error' => 'Unsupported request method']);
