<?php
session_start();
include './includes/connection.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT COUNT(*) AS total FROM favorites WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['count' => $row['total']]);
} else {
    echo json_encode(['count' => 0]);
}
