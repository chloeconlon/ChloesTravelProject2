<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([]);
    exit;
}

$selected_species = isset($_GET['species']) ? $_GET['species'] : "";

$sql = "SELECT 
            s.sighting_id,   a.name AS animal_name,  d.name AS destination_name,  s.sighting_date, s.notes,s.image_url,  d.latitude, d.longitude
        FROM Animal_Sightings s
        JOIN Animals a ON s.animal_id = a.animal_id
        JOIN Destinations d ON s.destination_id = d.destination_id";
$params = [];
$types = "";

if (!empty($selected_species)) {
    $sql .= " WHERE a.species = ?";
    $types = "s";
    $params[] = $selected_species;
}
$sql .= " ORDER BY s.sighting_date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$sightings = [];
while ($row = $result->fetch_assoc()) {
    $row['latitude'] = $row['latitude'] === null ? null : floatval($row['latitude']);
    $row['longitude'] = $row['longitude'] === null ? null : floatval($row['longitude']);
    $sightings[] = $row;
}

echo json_encode($sightings);

$stmt->close();
$conn->close();
?>
