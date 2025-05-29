<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Update host to "localhost" if that's what you use for local dev
$conn = new mysqli("localhost", "root", "", "travel_db");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$destination_name = trim($_POST['destination'] ?? '');
$rating = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

// Use the session user_id, fallback to 1 if not logged in
$user_id = $_SESSION['user_id'] ?? 1;

if (!$destination_name || !$rating || !$comment) {
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    $conn->close();
    exit;
}

// Get destination_id from destination name
$stmt = $conn->prepare("SELECT destination_id FROM Destinations WHERE name = ?");
$stmt->bind_param("s", $destination_name);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $destination_id = $row['destination_id'];
} else {
    echo json_encode(['success' => false, 'error' => 'Destination not found']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Insert review
$stmt = $conn->prepare("INSERT INTO Reviews (user_id, destination_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("iiis", $user_id, $destination_id, $rating, $comment);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>