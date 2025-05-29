<?php
session_start();

// Connect to database
$servername = "ChloesTravelProject";
$username = "root";
$password = "";
$dbname = "travel_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection worked
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

// Check if avatar was sent
if (!isset($_POST['avatar'])) {
    http_response_code(400);
    echo json_encode(["error" => "Avatar not provided"]);
    exit;
}

$avatar = $_POST['avatar']; // Avatar file name, like "avatar2.jpg"
$userId = $_SESSION['user_id'];

// Prepare SQL to update avatar
$stmt = $conn->prepare("UPDATE Users SET avatar = ? WHERE user_id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $conn->error]);
    exit;
}

// Bind values and run query
$stmt->bind_param("si", $avatar, $userId);
$stmt->execute();

// Check if query gave error
if ($stmt->error) {
    http_response_code(500);
    echo json_encode(["error" => "Update error: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

// Save avatar info in session and cookie
$_SESSION['selected_avatar'] = $avatar;
setcookie("selected_avatar", $avatar, time() + (86400 * 30), "/", "", false, true);

// Tell user if avatar changed or stayed the same
if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Avatar updated successfully"]);
} else {
    echo json_encode(["status" => "no change", "message" => "Avatar was already set to this value"]);
}

$stmt->close();
$conn->close();
?>