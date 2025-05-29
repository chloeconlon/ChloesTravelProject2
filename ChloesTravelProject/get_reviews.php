<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "travel_db");
if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

// Allow filtering by destination Name
$destination_name = isset($_GET['destination_name']) ? trim($_GET['destination_name']) : null;

$sql = "SELECT r.review_id, r.user_id, r.destination_id, d.name as destination_name, r.rating, r.comment, r.created_at 
        FROM Reviews r 
        JOIN Destinations d ON r.destination_id = d.destination_id";

if ($destination_name) {
    $sql .= " WHERE d.name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $destination_name);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$data = [];
while ($row = $result->fetch_assoc())
    $data[] = $row;
echo json_encode($data);

if (isset($stmt))
    $stmt->close();
$conn->close();
?>