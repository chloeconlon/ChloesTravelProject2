<?php
session_start();
$servername = "ChloesTravelProject";
$username = "root";
$password = "";
$dbname = "travel_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 6;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$sql = "SELECT * FROM Animals ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-sm-6 col-md-4 col-lg-3 d-flex justify-content-center">';
        echo '<div class="animal-card">';
        echo '<img src="' . htmlspecialchars($row['image_url'] ?? '') . '" alt="' . htmlspecialchars($row['name'] ?? '') . '">';
        echo '<h5>' . htmlspecialchars($row['name'] ?? '') . '</h5>';
        echo '<p><strong>Species:</strong> ' . htmlspecialchars($row['species'] ?? '') . '</p>';
        echo '<p><strong>Habitat:</strong> ' . htmlspecialchars($row['habitat'] ?? '') . '</p>';
        echo '<p>' . htmlspecialchars($row['description'] ?? '') . '</p>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p class="text-center">No more animals to display.</p>';
}

$conn->close();
?>