<?php
session_start();

// Database connection
$servername = "ChloesTravelProject";
$username = "root";
$password = "";
$dbname = "travel_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// FIX 1: Better avatar initialization logic
$avatar = 'avatar0.jpg'; // Default avatar

if (isset($_SESSION['user_id'])) {
    // If user selects a new avatar, update it first
    if (isset($_GET['avatar_id'])) {
        $avatar_id = (int) $_GET['avatar_id'];
        $new_avatar = 'avatar' . $avatar_id . '.jpg';

        // Update database
        $stmt = $conn->prepare("UPDATE Users SET avatar = ? WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $new_avatar, $_SESSION['user_id']);
            $stmt->execute();
            $stmt->close();
        }

        // Update session and cookie
        $_SESSION['selected_avatar'] = $new_avatar;
        setcookie("selected_avatar", $new_avatar, time() + (86400 * 30), "/", "", false, true);

        $avatar = $new_avatar;
    } else {
        // Load avatar from session, cookie, or database
        if (!empty($_SESSION['selected_avatar'])) {
            $avatar = $_SESSION['selected_avatar'];
        } elseif (isset($_COOKIE['selected_avatar']) && !empty($_COOKIE['selected_avatar'])) {
            $avatar = $_COOKIE['selected_avatar'];
            $_SESSION['selected_avatar'] = $avatar; // Restore to session
        } else {
            // Fetch from database
            $stmt = $conn->prepare("SELECT avatar FROM Users WHERE user_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    $avatar = !empty($user['avatar']) ? $user['avatar'] : 'avatar0.jpg';
                    $_SESSION['selected_avatar'] = $avatar;
                    setcookie("selected_avatar", $avatar, time() + (86400 * 30), "/", "", false, true);
                }
                $stmt->close();
            }
        }
    }
}

// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    setcookie("selected_avatar", "", time() - 3600, "/");
    header("Location: login_signup.php");
    exit();
}

// Extract avatar number for display
$avatar_number = 0;
if (preg_match('/avatar(\d+)\.jpg/', $avatar, $matches)) {
    $avatar_number = (int) $matches[1];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Chloe's Travel Project</title>
    <link rel="stylesheet" href="style/home.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f8f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #8fbc8f;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-right a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            margin: 0 10px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .navbar-right a:hover {
            background-color: #6c9e6c;
        }

        .mainsection {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, #8fbc8f, #f5fdf5);
            color: white;
        }

        /*Avatar */
        .avatar-selection {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .avatar-selection img {
            margin: 10px;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            cursor: pointer;
            transition: transform 0.3s;
            border: 4px solid transparent;
        }

        .avatar-selection img:hover {
            transform: scale(1.1);
            border-color: #fff;
        }

        .avatar-selection img.selected {
            border-color: #ffff00;
            transform: scale(1.1);
        }

        .logout-btn {
            padding: 10px 20px;
            background-color: #8fbc8f;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 1.1rem;
        }

        .logout-btn:hover {
            background-color: #6c9e6c;
        }

        .current-avatar {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-container">
            <div class="navbar-right">
                <a href="index.php">Home</a>
                <a href="animals.php">Animals</a>
                <a href="animal_sightings.php">Animal Sightings</a>
                <a href="destinations.php">Destinations</a>
                <a href="reviews.php">Reviews</a>
                <a href="login_signup.php">Login</a>
            </div>
        </div>
    </div>

    <!-- Profile Section -->
    <div class="mainsection">
        <h1>Your Profile</h1>
        <p>Change your avatar or log out.</p>

        <div class="current-avatar">
            <h3>Current Avatar:</h3>
            <img src="images/Avatars/<?php echo htmlspecialchars($avatar); ?>" alt="Current Avatar" width="130px"
                height="130px" style="border-radius: 50%;">
        </div>

        <div>
            <h3>Select an Avatar:</h3>
            <div class="avatar-selection">
                <?php
                for ($i = 1; $i <= 10; $i++) {
                    $selected_class = ($i == $avatar_number) ? 'selected' : '';
                    echo '<a href="profile.php?avatar_id=' . $i . '">
                            <img src="images/Avatars/avatar' . $i . '.jpg" alt="Avatar ' . $i . '" class="' . $selected_class . '" />
                          </a>';
                }
                ?>
            </div>
        </div>

        <div>
            <a href="profile.php?logout=true">
                <button class="logout-btn">Log Out</button>
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 Chloe's Travel Project | Made with ❤️</p>
    </footer>
</body>

</html>