<?php

//Debigging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "ChloesTravelProject";
$username = "root";
$password = "";
$dbname = "travel_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email or username is already taken by another user
    $sql = "SELECT * FROM Users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'Email or username already taken';
    } else {
        // Hash the password before saving to database
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the db
        $sql = "INSERT INTO Users (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password_hash);

        if ($stmt->execute()) {
            echo 'Success';
        } else {
            echo 'Error: ' . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>