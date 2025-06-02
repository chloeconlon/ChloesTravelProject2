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

        .form-row {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .form-row>div {
            flex: 1;
        }

        .location-helper {
            background: #f0f8ff;
            padding: 10px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 0.9em;
        }

        .coordinates-display {
            background: #e8f5e8;
            padding: 8px;
            border-radius: 4px;
            margin-top: 5px;
            font-family: monospace;
            font-size: 0.9em;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        @media (max-width: 600px) {
            .navbar-container {
                flex-direction: column;
                gap: 10px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
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
                    <a href="destinations.php">Destinations</a>
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

        <!-- Enhanced form for adding new sighting -->
        <div class="add-sighting-form">
            <h2>Add a New Sighting</h2>

            <div class="location-helper">
                <strong>💡Tips:</strong>
                <ul>
                    <li><strong>🖱️ Click on the map above</strong> to automatically set coordinates</li>
                    <li><strong>⌨️ Manually enter</strong> any destination name and coordinates</li>
                    <li><strong>🔍 Find coordinates</strong> online (Google Maps, etc.) and paste them</li>
                </ul>

            </div>

            <form id="sightingForm" enctype="multipart/form-data" method="POST">
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
                </div>

                <div class="form-group">
                    <label for="animal_name">Animal Name:</label>
                    <input type="text" id="animal_name" name="animal_name" required placeholder="">
                </div>

                <div class="form-group">
                    <label for="place">🌍 Destination/Location Name:</label>
                    <input type="text" id="place" name="place" required placeholder="Enter any location">

                </div>

                <div class="form-row">
                    <div>
                        <label for="latitude">📍 Latitude:</label>
                        <input type="number" id="latitude" name="latitude" step="any" required
                            placeholder="e.g. 53.349805">
                    </div>
                    <div>
                        <label for="longitude">📍 Longitude:</label>
                        <input type="number" id="longitude" name="longitude" step="any" required
                            placeholder="e.g. -6.26031">
                    </div>
                </div>

                <div id="coordinatesDisplay" class="coordinates-display" style="display: none;"></div>

                <div class="form-group">
                    <label for="sighting_date">Date & Time:</label>
                    <input type="datetime-local" id="sighting_date" name="sighting_date" required>
                </div>

                <div class="form-group">
                    <label for="notes">Notes/Description:</label>
                    <textarea id="notes" name="notes" rows="3" required placeholder="Describe what you saw"></textarea>
                </div>

                <div class="form-group">
                    <label for="image">📷 Photo: </label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <button type="submit"
                    style="background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px;">
                    🐾 Submit Sighting
                </button>
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
        let selectedLatLng = null;

        function initMap() {
            if (!map) {
                map = L.map('map').setView([20, 0], 2);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                markersLayer = L.layerGroup().addTo(map);

                // Add click event to map for coordinate selection
                map.on('click', function (e) {
                    const lat = e.latlng.lat.toFixed(6);
                    const lng = e.latlng.lng.toFixed(6);

                    // Update form fields
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;

                    // Show coordinates
                    updateCoordinatesDisplay(lat, lng);

                    // Add temporary marker
                    if (selectedLatLng) {
                        map.removeLayer(selectedLatLng);
                    }
                    selectedLatLng = L.marker([lat, lng])
                        .addTo(map)
                        .bindPopup('📍 Selected location for new sighting')
                        .openPopup();
                });
            } else {
                markersLayer.clearLayers();
            }
        }

        function updateCoordinatesDisplay(lat, lng) {
            const display = document.getElementById('coordinatesDisplay');
            display.style.display = 'block';
            display.innerHTML = `Selected coordinates: ${lat}, ${lng}`;
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
                .then(data => renderSightings(data))
                .catch(error => {
                    console.error('Error loading sightings:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            loadSightings();

            // Set default date to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('sighting_date').value = now.toISOString().slice(0, 16);

            const speciesFilter = document.getElementById('speciesFilter');
            if (speciesFilter) {
                speciesFilter.addEventListener('change', () => {
                    loadSightings(speciesFilter.value);
                });
            }

            // Form submission with better error handling
            document.getElementById('sightingForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);

                // Validate required fields
                const species = formData.get('species');
                const animalName = formData.get('animal_name');
                const place = formData.get('place');
                const latitude = formData.get('latitude');
                const longitude = formData.get('longitude');
                const date = formData.get('sighting_date');
                const notes = formData.get('notes');

                if (!species || !animalName || !place || !latitude || !longitude || !date || !notes) {
                    document.getElementById('sightingFormMsg').innerHTML =
                        '<div class="error-message">❌ Please fill in all required fields</div>';
                    return;
                }

                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = '🔄 Adding sighting...';
                submitBtn.disabled = true;

                // Clear previous messages
                document.getElementById('sightingFormMsg').innerHTML = '';

                fetch('add_sighting.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            console.warn('Response is not JSON, content-type:', contentType);
                            return response.text().then(text => {
                                console.log('Response text:', text);
                                throw new Error('Server returned non-JSON response: ' + text.substring(0, 100));
                            });
                        }

                        return response.json();
                    })
                    .then(data => {
                        console.log('Success response:', data);
                        const msgDiv = document.getElementById('sightingFormMsg');
                        if (data.success) {
                            msgDiv.innerHTML = '<div class="success-message">✅ Sighting added successfully!</div>';
                            form.reset();
                            // Reset date to current time
                            const now = new Date();
                            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                            document.getElementById('sighting_date').value = now.toISOString().slice(0, 16);

                            loadSightings();

                            // Clear selected marker
                            if (selectedLatLng) {
                                map.removeLayer(selectedLatLng);
                                selectedLatLng = null;
                            }
                            document.getElementById('coordinatesDisplay').style.display = 'none';
                        } else {
                            msgDiv.innerHTML = '<div class="error-message">❌ Error: ' +
                                (data.error || "Could not add sighting.") + '</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        let errorMessage = '❌ ';
                        if (error.message.includes('Failed to fetch')) {
                            errorMessage += 'Cannot connect to server. Please check:<br>' +
                                '• Is your web server running?<br>' +
                                '• Is add_sighting.php in the right location?<br>' +
                                '• Check browser console for details';
                        } else if (error.message.includes('HTTP error')) {
                            errorMessage += 'Server error: ' + error.message;
                        } else if (error.message.includes('non-JSON')) {
                            errorMessage += 'Server configuration issue: ' + error.message;
                        } else {
                            errorMessage += 'Error: ' + error.message;
                        }
                        document.getElementById('sightingFormMsg').innerHTML =
                            '<div class="error-message">' + errorMessage + '</div>';
                    })
                    .finally(() => {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    });
            });
        });
    </script>
</body>

</html>
