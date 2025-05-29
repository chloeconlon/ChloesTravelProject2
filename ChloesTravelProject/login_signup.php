<!DOCTYPE html>
<html lang="en">
<?php
$avatar = 'avatar0.jpg'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Signup</title>

    <style>

    </style>
    <header>
        <!-- Navbar -->
        <div class="navbar">
            <div class="navbar-container">
                <div class="navbar-right">
                    <a href="index.php">Home</a>
                    <a href="animals.php"> Animals</a>
                    <a href="animal_sightings.php">Animal Sightings</a>
                    <a href="destinations.php">Destinations</a>
                    <a href="reviews.php">Reviews</a>
                    <a href="login_signup.php " class="active">Login</a>
                </div>
            </div>

        </div>
    </header>
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="style/login.css">


</head>

<body>


    <div class="container" id="form-container">
        <h2>Login</h2>
        <form id="login-form">
            <input type="email" id="login-email" placeholder="Email" required>
            <input type="password" id="login-password" placeholder="Password" required>
            <button type="submit" class="button">Login</button>
        </form>
        <div class="toggle">
            <p>Don't have an account? <a href="#" id="toggle-to-signup">Sign up</a></p>
        </div>
        <div class="message" id="login-message"></div>
    </div>

    <div class="container" id="signup-container" style="display: none;">
        <h2>Sign Up</h2>
        <form id="signup-form">
            <input type="text" id="signup-username" placeholder="Username" required>
            <input type="email" id="signup-email" placeholder="Email" required>
            <input type="password" id="signup-password" placeholder="Password" required>
            <button type="submit" class="button">Sign Up</button>
        </form>
        <div class="toggle">
            <p>Already have an account? <a href="#" id="toggle-to-login">Login</a></p>
        </div>
        <div class="message" id="signup-message"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle between login and signup forms
        $('#toggle-to-signup').click(function () {
            $('#form-container').hide();
            $('#signup-container').show();
        });

        $('#toggle-to-login').click(function () {
            $('#signup-container').hide();
            $('#form-container').show();
        });

        // Handle login form submission
        $('#login-form').submit(function (event) {
            event.preventDefault();
            const email = $('#login-email').val();
            const password = $('#login-password').val();

            $.ajax({
                url: 'login.php',
                type: 'POST',
                data: { email: email, password: password },
                success: function (response) {
                    if (response === 'Success') {
                        window.location.href = 'index.php'; // Redirect to dashboard
                    } else {
                        $('#login-message').text(response); // Show error message
                    }
                },
                error: function (xhr, status, error) {
                    $('#login-message').text('Error: ' + error);
                }
            });
        });

        // Handle signup form submission
        $('#signup-form').submit(function (event) {
            event.preventDefault();
            const username = $('#signup-username').val();
            const email = $('#signup-email').val();
            const password = $('#signup-password').val();

            $.ajax({
                url: 'signup.php',
                type: 'POST',
                data: { username: username, email: email, password: password },
                success: function (response) {
                    if (response === 'Success') {
                        alert('Signup successful, you can now login!');
                        $('#toggle-to-login').click();
                    } else {
                        $('#signup-message').text(response); // Show error message
                    }
                },
                error: function (xhr, status, error) {
                    $('#signup-message').text('Error: ' + error);
                }
            });
        });
    </script>
    <!-- Footer -->
    <footer>
        <p>© 2024 Chloe's Travel Project | Made with ❤️</p>
    </footer>
</body>

<script src="script.js"></script>

</html>