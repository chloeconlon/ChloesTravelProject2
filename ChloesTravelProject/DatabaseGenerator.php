<!DOCTYPE html>
<html>

<head>
    <title>Creating Database Table</title>
</head>

<body>

    <?php
    $servername = "ChloesTravelProject";
    $username = "root";
    $password = "";
    $dbname = "travel_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Drop and re-create database
    $sql = "DROP DATABASE IF EXISTS $dbname;";
    $conn->query($sql);

    $sql = "CREATE DATABASE $dbname;";
    $conn->query($sql);

    $conn->close();

    // Reconnect to the new database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // USERS
    $sql = "CREATE TABLE Users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT 'avatar0.jpg',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;";
    $conn->query($sql);

    // DESTINATIONS
    $sql = "CREATE TABLE Destinations (
  destination_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  country VARCHAR(100),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  latitude DECIMAL(10,7),
  longitude DECIMAL(10,7),
  image_url VARCHAR(255)
) ENGINE=InnoDB;";
    $conn->query($sql);

    // ANIMALS
    $sql = "CREATE TABLE Animals (
  animal_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  species VARCHAR(50) NOT NULL,
  description TEXT,
  habitat VARCHAR(100),
  image_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;";
    $conn->query($sql);

    // REVIEWS
    $sql = "CREATE TABLE Reviews (
  review_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  destination_id INT,
  rating INT NOT NULL,
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (destination_id) REFERENCES Destinations(destination_id) ON DELETE CASCADE
) ENGINE=InnoDB;";
    $conn->query($sql);

    // ANIMAL_SIGHTINGS
    $sql = "CREATE TABLE Animal_Sightings (
  sighting_id INT AUTO_INCREMENT PRIMARY KEY,
  animal_id INT,
  destination_id INT,
  sighting_date DATETIME,
  image_url VARCHAR(255),
  notes TEXT,
  FOREIGN KEY (animal_id) REFERENCES Animals(animal_id) ON DELETE CASCADE,
  FOREIGN KEY (destination_id) REFERENCES Destinations(destination_id) ON DELETE CASCADE
) ENGINE=InnoDB;";
    $conn->query($sql);

    // Now you can safely insert data into your tables...
// (Insert statements go here)
    


    // INSERTING ANIMAL ENTRIES
    $sql = "INSERT INTO Animals (animal_id, name, species, description, habitat, created_at) VALUES
(1, 'Charlie', 'Dog', 'A friendly golden retriever who loves playing fetch.', 'Domestic', '2024-01-01 08:30:00'),
(2, 'Mittens', 'Cat', 'A curious tabby cat that enjoys climbing.', 'Domestic', '2024-01-02 09:00:00'),
(3, 'Benny', 'Rabbit', 'A small white rabbit with long ears.', 'Meadow', '2024-01-03 10:00:00'),
(4, 'Polly', 'Parrot', 'A green parrot with a red beak that talks.', 'Tropical Forest', '2024-01-04 11:00:00'),
(5, 'Sammy', 'Turtle', 'A slow-moving turtle that lives near water.', 'Wetlands', '2024-01-05 12:30:00'),
(6, 'Luna', 'Horse', 'A strong black horse used for riding.', 'Grasslands', '2024-01-06 13:45:00'),
(7, 'Rex', 'Iguana', 'A large iguana that loves to sunbathe.', 'Desert', '2024-01-07 14:20:00'),
(8, 'Goldie', 'Fish', 'A small goldfish that lives in a tank.', 'Aquatic', '2024-01-08 15:00:00'),
(9, 'Spike', 'Hedgehog', 'A spiky little animal that curls into a ball.', 'Forest', '2024-01-09 16:30:00'),
(10, 'Bella', 'Dog', 'A playful beagle with lots of energy.', 'Domestic', '2024-01-10 17:00:00'),
(11, 'Max', 'Cat', 'A fluffy Maine Coon with striking green eyes.', 'Domestic', '2024-01-11 18:20:00'),
(12, 'Shadow', 'Wolf', 'A large gray wolf with piercing yellow eyes.', 'Forest', '2024-01-12 19:00:00'),
(13, 'Ruby', 'Fox', 'A cunning red fox that is quick and agile.', 'Forest', '2024-01-13 20:15:00'),
(14, 'Oscar', 'Otter', 'A playful otter that loves sliding into the water.', 'River', '2024-01-14 07:00:00'),
(15, 'Coco', 'Monkey', 'A mischievous capuchin monkey.', 'Tropical Forest', '2024-01-15 08:45:00'),
(16, 'Daisy', 'Cow', 'A gentle dairy cow with a shiny black coat.', 'Farmland', '2024-01-16 09:30:00'),
(17, 'Finn', 'Dolphin', 'A social and intelligent bottlenose dolphin.', 'Ocean', '2024-01-17 10:30:00'),
(18, 'Whiskers', 'Cat', 'A Siamese cat with a regal demeanor.', 'Domestic', '2024-01-18 11:15:00'),
(19, 'Penny', 'Pig', 'A pink pig that loves rolling in the mud.', 'Farmland', '2024-01-19 12:00:00'),
(20, 'Zeus', 'Eagle', 'A majestic bald eagle soaring high in the sky.', 'Mountains', '2024-01-20 13:30:00'),
(21, 'Rocky', 'Goat', 'A mountain goat that climbs steep slopes.', 'Mountains', '2024-01-21 14:15:00'),
(22, 'Jasper', 'Bear', 'A large brown bear found near rivers.', 'Forest', '2024-01-22 15:00:00'),
(23, 'Willow', 'Deer', 'A graceful deer with a sleek brown coat.', 'Forest', '2024-01-23 16:45:00'),
(24, 'Amber', 'Kangaroo', 'A hopping kangaroo with a baby in her pouch.', 'Grasslands', '2024-01-24 17:30:00'),
(25, 'Ginger', 'Cat', 'An orange tabby cat with a sweet personality.', 'Domestic', '2024-01-25 18:15:00'),
(26, 'Bolt', 'Dog', 'A fast greyhound that loves running.', 'Domestic', '2024-01-26 19:00:00'),
(27, 'Oliver', 'Horse', 'A majestic Arabian horse with a shiny coat.', 'Grasslands', '2024-01-27 07:00:00'),
(28, 'Blaze', 'Falcon', 'A peregrine falcon known for its speed.', 'Mountains', '2024-01-28 08:30:00'),
(29, 'Lily', 'Swan', 'A graceful white swan that glides on water.', 'Lakes', '2024-01-29 09:15:00'),
(30, 'Ruby_Tiger', 'Tiger', 'A fierce Bengal tiger with striking stripes.', 'Jungle', '2024-01-30 10:00:00');
";
    if ($conn->query($sql) === TRUE) {
        echo "Animal Table entries created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $sql = "INSERT INTO Destinations (name, description, country, created_at, latitude, longitude, image_url) VALUES
('Moai Statues', 'Massive stone statues on Easter Island.', 'Chile', '2024-03-22 19:30:00', -27.114410, -109.425270, 'images/MoaiStatuesChile.jpg'),
('Kruger National Park', 'A premier wildlife safari destination.', 'South Africa', '2024-03-23 20:30:00', -23.988400, 31.554700, 'images/KrugerNationalPark.jpg'),
('Ha Long Night Market', 'A vibrant marketplace for local goods.', 'Vietnam', '2024-03-24 07:15:00', 20.951000, 107.087500, 'images/HaLongMarket.jpg'),
('Pamukkale', 'Natural travertine terraces with thermal waters.', 'Turkey', '2024-03-25 08:00:00', 37.921389, 29.119167, 'images/Pamukkale.jpg'),
('Dubrovnik Old Town', 'A walled city with medieval charm.', 'Croatia', '2024-03-26 09:30:00', 42.640278, 18.108333, 'images/Dubrovnik.jpg'),
('Arctic Circle', 'A polar region known for its icy wilderness.', 'Norway', '2024-03-27 10:15:00', 66.562222, 25.847778, 'images/ArcticCircle.jpg'),
('Etosha National Park', 'A vast salt pan and wildlife haven.', 'Namibia', '2024-03-28 11:45:00', -18.775000, 16.319444, 'images/EtoshaNationalPark.jpg'),
('Chamonix', 'A famous ski resort town in the Alps.', 'France', '2024-03-29 12:30:00', 45.923697, 6.869433, 'images/Chamonix.jpg'),
('Komodo Island', 'Home to the famous Komodo dragons.', 'Indonesia', '2024-03-30 13:15:00', -8.550000, 119.466667, 'images/KomodoIsland.jpg'),
('Dead Sea', 'A salt lake known for its buoyancy and mineral-rich waters.', 'Jordan', '2024-03-31 14:45:00', 31.559000, 35.473200, 'images/DeadSea.jpg'),
('Palawan', 'A stunning island province with turquoise waters.', 'Philippines', '2024-04-01 15:30:00', 9.834949, 118.738724, 'images/Palawan.jpg'),
('Isle of Skye', 'A rugged and picturesque island in Scotland.', 'United Kingdom', '2024-04-02 16:15:00', 57.535000, -6.226000, 'images/IsleofSkye.jpg'),
('Fjordland National Park', 'A dramatic landscape of fjords and rainforests.', 'New Zealand', '2024-04-03 17:00:00', -45.415000, 167.718000, 'images/FjordlandNationalPark.jpg'),
('Tuscany', 'A region known for its rolling hills and vineyards.', 'Italy', '2024-04-04 18:30:00', 43.771051, 11.248621, 'images/Tuscany.jpg'),
('Sossusvlei', 'A desert valley with towering red dunes.', 'Namibia', '2024-04-05 19:15:00', -24.731944, 15.362500, 'images/Sossusvlei.jpg'),
('Lake Titicaca', 'The largest lake in South America, shared by Peru and Bolivia.', 'Peru/Bolivia', '2024-04-06 20:30:00', -15.765278, -69.420833, 'images/LakeTiticaca.jpg'),
('Easter Markets', 'Seasonal markets celebrating traditions.', 'Czech Republic', '2024-04-07 07:15:00', 50.087465, 14.421254, 'images/EasterMarkets.jpg'),
('Mývatn', 'A geothermally active area with volcanic craters.', 'Iceland', '2024-04-08 08:00:00', 65.616667, -17.000000, 'images/Mývatn.jpg'),
('Karnak Temple', 'A massive ancient temple complex in Luxor.', 'Egypt', '2024-04-09 09:30:00', 25.718889, 32.657222, 'images/KarnakTemple.jpg');

";

    if ($conn->query($sql) === TRUE) {
        echo "Destinations Table entries created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $sql = "INSERT INTO Users (username, email, password_hash, created_at) VALUES
('john_doe', 'john.doe@example.com', '5f4dcc3b5aa765d61d8327deb882cf99', '2024-01-01 08:00:00'),
('mason_king', 'mason.king@example.com', 'bf4dcc3b5aa765d61d8327deb882cfpp', '2024-01-26 19:00:00'),
('chloe_wright', 'chloe.wright@example.com', 'cf4dcc3b5aa765d61d8327deb882cfqq', '2024-01-27 20:00:00'),
('henry_edwards', 'henry.edwards@example.com', 'df4dcc3b5aa765d61d8327deb882cfrr', '2024-01-28 07:15:00'),
('ella_reed', 'ella.reed@example.com', 'ef4dcc3b5aa765d61d8327deb882cfss', '2024-01-29 08:45:00'),
('sebastian_perez', 'sebastian.perez@example.com', 'ff4dcc3b5aa765d61d8327deb882cftt', '2024-01-30 09:30:00'),
('sophie_carter', 'sophie.carter@example.com', '0f4dcc3b5aa765d61d8327deb882cfuu', '2024-01-31 10:15:00'),
('lucy_martinez', 'lucy.martinez@example.com', '1f4dcc3b5aa765d61d8327deb882cfvv', '2024-02-01 11:00:00'),
('ryan_anderson', 'ryan.anderson@example.com', '2f4dcc3b5aa765d61d8327deb882cfww', '2024-02-02 12:15:00'),
('hannah_taylor', 'hannah.taylor@example.com', '3f4dcc3b5aa765d61d8327deb882cfxx', '2024-02-03 13:30:00'),
('matthew_hill', 'matthew.hill@example.com', '4f4dcc3b5aa765d61d8327deb882cfyy', '2024-02-04 14:00:00'),
('zoe_scott', 'zoe.scott@example.com', '5f4dcc3b5aa765d61d8327deb882cfzz', '2024-02-05 15:15:00'),
('harry_white', 'harry.white@example.com', '7f4dcc3b5aa765d61d8327deb882cfgg', '2024-03-31 10:30:00');";

    if ($conn->query($sql) === TRUE) {
        echo "Users Table entries created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $sql = " INSERT INTO Animal_Sightings (animal_id, destination_id, sighting_date, notes) VALUES
(1, 1, '2024-01-10 14:30:00', 'Charlie spotted playing in the park.'),
(2, 2, '2024-01-11 09:00:00', 'Mittens seen climbing a tree.'),
(3, 3, '2024-01-12 10:15:00', 'Benny hopping around the meadow.'),
(4, 4, '2024-01-13 11:45:00', 'Polly talking to tourists.'),
(5, 5, '2024-01-14 12:30:00', 'Sammy basking in the sun.'),
(1, 2, '2024-01-15 14:00:00', 'Charlie chasing a ball.'),
(2, 3, '2024-01-16 09:30:00', 'Mittens exploring the meadow.'),
(2, 4, '2024-02-10 09:30:00', 'Mittens exploring a new area.'),
(3, 5, '2024-02-11 10:45:00', 'Benny hiding under a log.'),
(4, 1, '2024-02-12 11:15:00', 'Polly singing loudly.'),
(5, 2, '2024-02-13 12:00:00', 'Sammy resting near water.'),
(1, 4, '2024-02-14 14:45:00', 'Charlie running in circles.'),
(2, 5, '2024-02-15 09:15:00', 'Mittens playing with leaves.'),
(3, 1, '2024-02-16 10:30:00', 'Benny hopping around happily.'),
(4, 2, '2024-02-17 11:00:00', 'Polly interacting with children.'),
(5, 3, '2024-02-18 12:45:00', 'Sammy slowly moving around.');";

    if ($conn->query($sql) === TRUE) {
        echo "Animal Sightings Table entries created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $sql = "INSERT INTO Reviews (user_id, destination_id, rating, comment) VALUES
(1, 1, 5, 'Amazing experience, highly recommend!'),
(2, 2, 4, 'Great place, but a bit crowded.'),
(3, 3, 3, 'Average, nothing special.'),
(4, 4, 5, 'Loved every moment of it!'),
(5, 5, 2, 'Not worth the hype.'),
(1, 2, 4, 'Nice place, would visit again.'),
(2, 3, 3, 'It was okay, not great.'),
(3, 4, 5, 'Fantastic, will come back!'),
(4, 5, 1, 'Terrible experience.'),
(5, 1, 4, 'Good, but could be better.'),
(1, 3, 3, 'Mediocre, nothing to write home about.'),
(2, 4, 5, 'Absolutely wonderful!'),
(3, 5, 2, 'Disappointing.'),
(4, 1, 4, 'Pretty good overall.'),
(5, 2, 3, 'Just okay.'),
(1, 4, 5, 'Loved it!'),
(2, 5, 2, 'Not impressed.'),
(3, 1, 4, 'Quite enjoyable.'),
(4, 2, 3, 'It was fine.'),
(5, 3, 5, 'Exceeded expectations!'),
(1, 5, 2, 'Would not recommend.'),
(2, 1, 4, 'Pleasant experience.'),
(3, 2, 3, 'Average at best.'),
(4, 3, 5, 'Fantastic place!'),
(5, 4, 1, 'Very disappointing.'),
(1, 2, 4, 'Nice, but not amazing.'),
(2, 3, 3, 'It was alright.'),
(3, 4, 5, 'Absolutely loved it!'),
(4, 5, 2, 'Not worth the visit.'),
(5, 1, 4, 'Good, but not great.'),
(1, 3, 3, 'Just okay.'),
(2, 4, 5, 'Wonderful experience!'),
(3, 5, 2, 'Quite disappointing.'),
(4, 1, 4, 'Pretty good.'),
(5, 2, 3, 'Mediocre.'),
(1, 4, 5, 'Loved every bit of it!'),
(2, 5, 2, 'Not worth it.'),
(3, 1, 4, 'Enjoyable.'),
(4, 2, 3, 'It was fine.'),
(5, 3, 5, 'Amazing place!');
";

    if ($conn->query($sql) === TRUE) {
        echo "Review Table entries created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error;
    }
    $conn->close();
    ?>

</body>

</html>