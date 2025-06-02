<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$country = isset($_GET['country']) ? $_GET['country'] : null;

// SQL query to fetch destinations
$sql = "SELECT destination_id, name, description, country, image_url, created_at 
        FROM Destinations WHERE 1";

// Filter by country if provided
if ($country) {
    $sql .= " AND country = ?";
}

$stmt = $conn->prepare($sql);

if ($country) {
    $stmt->bind_param("s", $country);
}

$stmt->execute();
$result = $stmt->get_result();

$destinations = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $destinations[] = $row;
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($destinations);

$stmt->close();
$conn->close();
?>
