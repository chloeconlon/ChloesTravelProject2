<?php
session_start();
//debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "ChloesTravelProject";
$username = "root";
$password = "";
$dbname = "travel_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stored_hash = $user['password_hash'];

        // Debug 
        error_log("Debug - Email: " . $email);
        error_log("Debug - Input Password: " . $password);
        error_log("Debug - Stored Hash: " . $stored_hash);
        error_log("Debug - Hash Length: " . strlen($stored_hash));

        $password_correct = false;

        // different password verification methods
        if (strlen($stored_hash) == 32) {
            // MD5 hash (32 characters)
            $input_hash = md5($password);
            error_log("Debug - MD5 of input: " . $input_hash);
            if ($input_hash === $stored_hash) {
                $password_correct = true;
                error_log("Debug - MD5 match successful");
            }
        } elseif (strlen($stored_hash) >= 60 && (substr($stored_hash, 0, 4) == '$2y$' || substr($stored_hash, 0, 4) == '$2b$')) {

            if (password_verify($password, $stored_hash)) {
                $password_correct = true;
                error_log("Debug - bcrypt match successful");
            }
        } else {

            $input_hash = md5($password);
            if ($input_hash === $stored_hash) {
                $password_correct = true;
                error_log("Debug - Fallback MD5 match successful");
            } elseif (password_verify($password, $stored_hash)) {
                $password_correct = true;
                error_log("Debug - Fallback bcrypt match successful");
            }
        }
        // Setting user session
        if ($password_correct) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $avatar = 'avatar0.jpg';
            $avatar = !empty($user['avatar']) ? $user['avatar'] : 'avatar0.jpg';
            $_SESSION['selected_avatar'] = $avatar;


            setcookie("selected_avatar", $avatar, time() + (86400 * 30), "/", "", false, true);

            echo 'Success';
        } else {
            error_log("Debug - Password verification failed");
            echo 'Incorrect password';
        }
    } else {
        echo 'No user found with this email';
    }

    $stmt->close();
}

// Password reset 
if (isset($_GET['reset_password'])) {
    $email = $_GET['email'];
    $new_password = $_GET['password'];

    if ($email && $new_password) {
        // Using MD5 
        $new_hash = md5($new_password);

        $sql = "UPDATE Users SET password_hash = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_hash, $email);

        if ($stmt->execute()) {
            echo "Password reset successful for " . $email . " to " . $new_password;
        } else {
            echo "Password reset failed";
        }
        $stmt->close();
    }
}

$conn->close();
?>