<?php
session_start();
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_db";

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch species for dropdown
$species_sql = "SELECT DISTINCT species FROM Animals ORDER BY species ASC";
$species_result = $conn->query($species_sql);

$species_options = [];
if ($species_result && $species_result->num_rows > 0) {
    while ($row = $species_result->fetch_assoc()) {
        $species_options[] = $row['species'];
    }
}

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
    <title>Animal Sightings</title>
    <link rel="stylesheet" href="style/animal_sightings.css">
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="style/nav_footer.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .navbar-avatar {
            margin-left: 20px;
        }

        .navbar-avatar img {
            border-radius: 50%;
            cursor: pointer;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
        }

        @media (max-width: 600px) {
            .navbar-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="navbar-container">
                <div class="navbar-right">
                    <a href="index.php">Home</a>
                    <a href="animals.php">Animals</a>
                    <a href="animal_sightings.php" class="active">Animal Sightings</a>
                    <a href="destinations.php" class="active">Destinations</a>

                    <a href="reviews.php">Reviews</a>
                    <a href="login_signup.php">Login</a>
                </div>
                <div class="navbar-avatar">
                    <a href="profile.php">
                        <img src="images/Avatars/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" width="50"
                            height="50">
                    </a>
                </div>
            </div>
        </div>
    </header>
    <div class="container">
        <h1>Animal Sightings</h1>
        <label for="speciesFilter">Filter by Species:</label>
        <select id="speciesFilter">
            <option value="">All</option>
            <?php foreach ($species_options as $species): ?>
                <option value="<?php echo htmlspecialchars($species ?? ''); ?>">
                    <?php echo htmlspecialchars($species ?? ''); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <!-- MAP -->
        <div id="map" style="height: 400px; width: 100%; margin: 30px 0; border-radius: 12px;"></div>
        <div class="sightings-grid" id="sightingsGrid"></div>
        <!-- form for adding new sighting -->
        <div class="add-sighting-form">
            <h2>Add a New Sighting</h2>
            <form id="sightingForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="species">Species:</label>
                    <select id="species" name="species" required>
                        <option value="">Select Species</option>
                        <?php
                        $species_res = $conn->query("SELECT DISTINCT species FROM Animals ORDER BY species");
                        while ($row = $species_res->fetch_assoc()):
                            ?>
                            <option value="<?= htmlspecialchars($row['species']) ?>">
                                <?= htmlspecialchars($row['species']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label for="place">Destination:</label>
                    <input type="text" id="place" name="place" required>
                </div>
                <div class="form-group">
                    <label for="animal_name">Animal Name:</label>
                    <input type="text" id="animal_name" name="animal_name" required>
                </div>
                <div class="form-group">
                    <label for="latitude">Latitude:</label>
                    <input type="number" id="latitude" name="latitude" step="any" required placeholder="e.g. 53.349805">
                    <label for="longitude">Longitude:</label>
                    <input type="number" id="longitude" name="longitude" step="any" required
                        placeholder="e.g. -6.26031">
                </div>
                <div class="form-group">
                    <label for="sighting_date">Date:</label>
                    <input type="datetime-local" id="sighting_date" name="sighting_date" required>
                </div>
                <div class="form-group">
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Photo (optional):</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <button type="submit">Submit Sighting</button>
            </form>
            <div id="sightingFormMsg"></div>
        </div>
    </div>
    <footer>
        <p>© 2024 Chloe's Travel Project | Made with ❤️</p>
    </footer>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map, markersLayer;
        function initMap() {
            if (!map) {
                map = L.map('map').setView([20, 0], 2);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                markersLayer = L.layerGroup().addTo(map);
            } else {
                markersLayer.clearLayers();
            }
        }
        function addSightingsToMap(sightings) {
            if (!map) initMap();
            markersLayer.clearLayers();
            if (sightings.length === 0) return;
            let bounds = [];
            sightings.forEach(sighting => {
                if (sighting.latitude && sighting.longitude) {
                    const marker = L.marker([sighting.latitude, sighting.longitude])
                        .bindPopup(
                            `<strong>${sighting.animal_name}</strong><br>
                     <em>${sighting.destination_name}</em><br>
                     ${sighting.sighting_date}<br>
                     ${sighting.notes}${sighting.image_url ? `<br><img src="${sighting.image_url}" alt="Sighting Image" style="max-width:140px;max-height:140px;border-radius:6px;margin-top:6px;">` : ''}`
                        );
                    marker.addTo(markersLayer);
                    bounds.push([sighting.latitude, sighting.longitude]);
                }
            });
            if (bounds.length > 0) map.fitBounds(bounds, { padding: [40, 40] });
        }
        function renderSightings(sightings) {
            const grid = document.getElementById('sightingsGrid');
            grid.innerHTML = '';
            if (sightings.length === 0) {
                grid.innerHTML = '<p>No sightings yet for this selection.</p>';
                addSightingsToMap([]);
                return;
            }
            sightings.forEach(sighting => {
                grid.innerHTML += `
            <div class="sighting-card">
                ${sighting.image_url ? `<img src="${sighting.image_url}" alt="Sighting Image">` : `<img src="images/animals/default.jpg" alt="Default Image">`}
                <h3>${sighting.animal_name}</h3>
                <div class="sighting-meta">
                    <span>at <b>${sighting.destination_name}</b></span>
                </div>
                <div class="sighting-date">
                    <small>${sighting.sighting_date}</small>
                </div>
                <div class="sighting-notes">
                    ${sighting.notes}
                </div>
            </div>
        `;
            });
            addSightingsToMap(sightings);
        }
        function loadSightings(species = "") {
            fetch('get_sightings.php?species=' + encodeURIComponent(species))
                .then(response => response.json())
                .then(data => renderSightings(data));
        }
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            loadSightings();
            const speciesFilter = document.getElementById('speciesFilter');
            if (speciesFilter) {
                speciesFilter.addEventListener('change', () => {
                    loadSightings(speciesFilter.value);
                });
            }
            document.getElementById('sightingForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                fetch('add_sighting.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        const msgDiv = document.getElementById('sightingFormMsg');
                        if (data.success) {
                            msgDiv.textContent = "Sighting added!";
                            form.reset();
                            loadSightings();
                        } else {
                            msgDiv.textContent = "Error: " + (data.error || "Could not add sighting.");
                        }
                    })
                    .catch(() => {
                        document.getElementById('sightingFormMsg').textContent = "Network error.";
                    });
            });
        });
    </script>
</body>

</html>