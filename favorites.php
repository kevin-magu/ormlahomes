<?php
// Include the MySQLi database connection file
include('./includes/connection.php');

// Start session to access $_SESSION
session_start();

// Set JSON content type
header('Content-Type: application/json');

// Verify MySQLi connection
if (!$conn instanceof mysqli || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . ($conn->connect_error ?? 'Unknown error')
    ]);
    error_log('MySQLi connection failed: ' . ($conn->connect_error ?? 'Unknown error'), 3, './errors.log');
    exit;
}

// Check user authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to perform this action.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['property_id']) || !is_numeric($_GET['property_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Valid property ID is required.'
        ]);
        exit;
    }

    $propertyId = intval($_GET['property_id']);
    if ($propertyId <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid property ID.'
        ]);
        exit;
    }

    $stmt = $conn->prepare('SELECT * FROM favorites WHERE user_id = ? AND property_id = ?');
    if (!$stmt) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database query preparation failed.'
        ]);
        error_log('Prepare failed: ' . $conn->error, 3, './error_log.txt');
        exit;
    }

    $stmt->bind_param('ii', $userId, $propertyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $favorite = $result->fetch_assoc();

    echo json_encode([
        'success' => true,
        'favorited' => $favorite ? true : false,
        'message' => $favorite ? 'Property is in your favorites.' : 'Property is not favorited.'
    ]);
    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['property_id'], $data['action']) || !is_numeric($data['property_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Valid property ID and action are required.'
        ]);
        exit;
    }

    $propertyId = intval($data['property_id']);
    $action = $data['action'];

    if ($action === 'add') {
        $stmt = $conn->prepare('INSERT INTO favorites (user_id, property_id) VALUES (?, ?)');
        if (!$stmt) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database query preparation failed.'
            ]);
            error_log('Prepare failed: ' . $conn->error, 3, './errors.log');
            exit;
        }

        $stmt->bind_param('ii', $userId, $propertyId);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'favorited' => true,
                'propertyId' => $propertyId,
                'message' => 'Property added to your favorites.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'This property is already in your favorites.'
            ]);
            error_log('Insert failed: ' . $stmt->error, 3, './errors.log');
        }
        $stmt->close();
    } elseif ($action === 'remove') {
        $stmt = $conn->prepare('DELETE FROM favorites WHERE user_id = ? AND property_id = ?');
        if (!$stmt) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database query preparation failed.'
            ]);
            error_log('Prepare failed: ' . $conn->error, 3, './errors.log');
            exit;
        }

        $stmt->bind_param('ii', $userId, $propertyId);
        $stmt->execute();
        echo json_encode([
            'success' => true,
            'favorited' => false,
            'propertyId' => $propertyId,
            'message' => 'Property removed from your favorites.'
        ]);
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action.'
        ]);
    }
    exit;
}

// Default fallback for unsupported methods
http_response_code(405);
echo json_encode([
    'success' => false,
    'message' => 'Unsupported request method.'
]);
exit;
?>