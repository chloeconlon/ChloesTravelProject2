<!DOCTYPE html>
<html lang="en">
<?php

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
} ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations</title>

    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="style/nav_footer.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form select,
        .filter-form button {
            padding: 10px;
            margin-right: 10px;
        }

        .destinations-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .destination-card {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .destination-card img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .destination-card h3 {
            margin: 10px 0 5px;
        }

        .destination-card p {
            margin: 5px 0;
        }
    </style>
</head>
<header> <!-- Navbar -->
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


            <div class="navbar-avatar">
                <a href="profile.php">


                    <img src="images/Avatars/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" width="50px"
                        height="50px">
                </a>
            </div>
        </div>
    </div>
</header>

<body>


    <div class="container">
        <h1>Destinations</h1>

        <!-- Filter Form -->
        <div class="filter-form">
            <select id="countryFilter">
                <option value="">Select Country</option>
                <option value="Chile">Chile</option>
                <option value="South Africa">South Africa</option>
                <option value="Vietnam">Vietnam</option>
                <option value="Turkey">Turkey</option>
                <option value="Croatia">Croatia</option>
                <option value="Norway">Norway</option>
                <option value="Namibia">Namibia</option>
                <option value="France">France</option>
                <option value="Indonesia">Indonesia</option>
                <option value="Jordan">Jordan</option>
                <option value="Philippines">Philippines</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="New Zealand">New Zealand</option>
                <option value="Italy">Italy</option>
                <option value="Peru/Bolivia">Peru/Bolivia</option>
                <option value="Czech Republic">Czech Republic</option>
                <option value="Iceland">Iceland</option>
                <option value="Egypt">Egypt</option>
            </select>
            <button id="filterButton">Filter</button>
        </div>


        <div class="destinations-list" id="destinationsList">
            <!-- Destinations are dynamically inputed here -->
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            // fetch and display destinations
            function fetchDestinations(country = '') {
                $.ajax({
                    url: 'get_destinations.php',
                    type: 'GET',
                    data: { country: country },
                    success: function (data) {
                        displayDestinations(data);
                    },
                    error: function (xhr, status, error) {
                        alert('Error loading destinations: ' + error);
                    }
                });
            }

            // Display the fetched destinations
            function displayDestinations(destinations) {
                const destinationsList = $('#destinationsList');
                // Clear existing destinations list
                destinationsList.empty();

                if (destinations.length === 0) {
                    destinationsList.append('<p>No destinations found for this country.</p>');
                    return;
                }

                destinations.forEach(destination => {
                    destinationsList.append(`
                    <div class="destination-card">
                        <h3>${destination.name}</h3>
                        <p><strong>Country:</strong> ${destination.country}</p>
                        <p>${destination.description}</p>
                        ${destination.image_url ? `<img src="${destination.image_url}" alt="Destination Image">` : ''}
                        <p><strong>Added on:</strong> ${new Date(destination.created_at).toLocaleString()}</p>
                    </div>
                `);
                });
            }

            // Filter destinations
            $('#filterButton').click(function () {
                const country = $('#countryFilter').val();
                fetchDestinations(country);
            });


            fetchDestinations();
        });
    </script>

    <footer>
        <p>© 2024 Chloe's Travel Project | Made with ❤️</p>
    </footer>
</body>
<script src="script.js"></script>

</html>