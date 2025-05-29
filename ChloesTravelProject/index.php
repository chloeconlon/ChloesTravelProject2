<?php
session_start();

// Default avatar
$avatar = 'avatar0.jpg';

if (isset($_SESSION['user_id'])) {
    if (!empty($_SESSION['selected_avatar'])) {
        $avatar = $_SESSION['selected_avatar'];
    } elseif (isset($_COOKIE['selected_avatar']) && !empty($_COOKIE['selected_avatar'])) {
        $avatar = $_COOKIE['selected_avatar'];
        $_SESSION['selected_avatar'] = $avatar;
    } else {

        require_once 'DatabaseGenerator.php';
        $stmt = $conn->prepare("SELECT avatar FROM Users WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $avatar = !empty($user['avatar']) ? $user['avatar'] : 'avatar0.jpg';
                $_SESSION['selected_avatar'] = $avatar;
                setcookie("selected_avatar", $avatar, time() + (86400 * 30), "/", "localhost", false, true);

            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Chloe's Travel Project</title>

    <link rel="stylesheet" href="style/index.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8f0;
            color: #333;
        }

        /* Navbar */
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

        .navbar-avatar {
            margin-left: 20px;
        }

        .navbar-avatar img {
            border-radius: 50%;
            cursor: pointer;
        }


        /* Main Content */
        .mainsection {
            background: linear-gradient(135deg, #8fbc8f, #f5fdf5);
            color: white;
            text-align: center;
            padding: 100px 20px;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-container">
            <div class="navbar-right">
                <a href="index.php" class="active">Home</a>
                <a href="animals.php">Animals</a>
                <a href="animal_sightings.php">Animal Sightings</a>
                <a href="destinations.php">Destinations</a>
                <a href="reviews.php">Reviews</a>
                <a href="login_signup.php">Login</a>
            </div>
        </div>
        <div class="navbar-avatar">
            <a href="profile.php">


                <img src="images/Avatars/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" width="50px"
                    height="50px">
            </a>
        </div>
    </div>

    <!-- Main section -->
    <div class="mainsection">
        <div class="text1">
            <h1>Welcome to Chloe's Travel Project!</h1>
            <p>Explore the world of animals, discover unique sightings, and share your own adventures!</p>
            <button onclick="window.location.href='animal_sightings.php'">Get Started</button>
        </div>
        <div class="features">
            <h2>Our Features</h2>
            <div class="feature-cards">
                <div class="card">
                    <a href="animals.php" id="linkss">
                        <h3>Animal Encyclopedia</h3>
                        <p>Discover fascinating information about various animals.</p>
                    </a>
                </div>
                <div class="card">
                    <a href="animal_sightings.php" id="linkss">
                        <h3>Sightings Map</h3>
                        <p>Track and share animal sightings from around the globe.</p>
                    </a>
                </div>
                <div class="card">
                    <a href="reviews.php" id="linkss">
                        <h3>User Reviews</h3>
                        <p>Read and write reviews to share your travel experiences.</p>
                    </a>
                </div>
            </div>
        </div>


        <footer>
            <p>© 2024 Chloe's Travel Project | Made with ❤️</p>
        </footer>
</body>

<script src="script.js"></script>

</html>