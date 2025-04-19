<?php
// Include DB connection
include './includes/connection.php';

// Check if form data exists
if (isset($_POST['firstname']) && isset($_POST['password'])) {
    $name = trim($_POST['firstname']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, password) VALUES (?, ?)");
        $stmt->execute([$name, $password]);

        echo "User inserted successfully.";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Form data not received properly.";
}

$conn = null;
?>
