<?php

header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'DB connection failed']);
    exit;
}

// Get or create animal
$species = $_POST['species'] ?? '';
$animal_name = $_POST['animal_name'] ?? '';
$destination_name = $_POST['place'] ?? '';
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
$sighting_date = $_POST['sighting_date'] ?? '';
$notes = $_POST['notes'] ?? '';
$image_url = null;

if ($species && $animal_name) {
    $stmt = $conn->prepare("SELECT animal_id FROM Animals WHERE name=? AND species=?");
    $stmt->bind_param("ss", $animal_name, $species);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $animal_id = $row['animal_id'];
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO Animals (name, species) VALUES (?, ?)");
        $stmt_insert->bind_param("ss", $animal_name, $species);
        $stmt_insert->execute();
        $animal_id = $stmt_insert->insert_id;
        $stmt_insert->close();
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing animal name or species']);
    exit;
}

// Ensure destination exists
if ($destination_name) {
    $stmt = $conn->prepare("SELECT destination_id FROM Destinations WHERE name=?");
    $stmt->bind_param("s", $destination_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $destination_id = $row['destination_id'];
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO Destinations (name, latitude, longitude) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("sdd", $destination_name, $latitude, $longitude);
        $stmt_insert->execute();
        $destination_id = $stmt_insert->insert_id;
        $stmt_insert->close();
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Missing destination/place']);
    exit;
}

// Handle file upload 
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir))
        mkdir($target_dir, 0777, true);
    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $target_file = $target_dir . $filename;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_url = $target_file;
    }
}

$stmt = $conn->prepare("INSERT INTO Animal_Sightings (animal_id, destination_id, sighting_date, image_url, notes, latitude, longitude)
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisssdd", $animal_id, $destination_id, $sighting_date, $image_url, $notes, $latitude, $longitude);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
$stmt->close();
$conn->close();
?>
