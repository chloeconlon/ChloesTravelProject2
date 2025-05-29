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


// Default avatar or choosing avatar
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
    <title>Animals Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="style/nav_footer.css">
    <link rel="stylesheet" href="style/animalGallery.css">
    <style>
        .navbar-avatar {
            margin-left: 20px;
        }

        .navbar-avatar img {
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <header>
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
                    <img src="images/Avatars/<?php echo htmlspecialchars($avatar ?? ''); ?>" alt="Avatar" width="50"
                        height="50">
                </a>
            </div>
        </div>
    </header>

    <div class="container py-5">
        <h1 class="text-center my-4">Chloe's Animal Encyclopedia</h1>
        <div class="row justify-content-center" id="animal-list"></div>
        <div class="text-center">
            <button id="loadMoreBtn" class="btn btn-primary">Load More Animals</button>
        </div>
    </div>

    <footer class="text-center py-4">
        <p>© 2024 Chloe's Travel Project | Made with ❤️</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let limit = 6;
            let offset = 0;

            function loadAnimals() {
                fetch(`load_animals.php?limit=${limit}&offset=${offset}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(data => {
                        document.getElementById('animal-list').insertAdjacentHTML('beforeend', data);
                        offset += limit;
                    })
                    .catch(() => {
                        alert("Error loading animals.");
                    });
            }

            loadAnimals();

            document.getElementById('loadMoreBtn').addEventListener('click', function () {
                loadAnimals();
            });
        });
    </script>
    <script src="script.js"></script>
</body>

</html>