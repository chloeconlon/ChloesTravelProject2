<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews</title>
    <link rel="stylesheet" href="style/nav_footer.css">
    <link rel="stylesheet" href="style/styles.css">
    <link rel="stylesheet" href="style/reviews.css">
    <style>
        /* avatar in navbar */
        .navbar-avatar {
            margin-left: 20px;
        }


        .navbar-avatar img {
            border-radius: 50%;
            cursor: pointer;
        }

        /* FEATURE CARDS*/
        .feature-card {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            padding: 1.5rem 2rem;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1rem;
            align-items: start;
            border-left: 6px solid #4f8cff;
            position: relative;
        }

        .feature-card .icon {
            font-size: 2rem;
            color: #4f8cff;
        }


        .feature-card .content h3 {
            margin: 0;
            color: #1b2845;
            font-size: 1.2rem;
        }

        /* Star rating  */
        .feature-card .content .rating {
            color: #ffc107;
            margin: 0.3rem 0;
        }


        .feature-card .content p {
            margin: 0.5rem 0;
            color: #374151;
        }


        .feature-card .content small {
            color: #a0aec0;
        }
    </style>
</head>

<body>
    <!-- Load avatar from PHP -->
    <?php include 'avatar_load.php'; ?>

    <header>
        <div class="navbar">
            <div class="navbar-container">
                <div class="navbar-right">

                    <a href="index.php">Home</a>
                    <a href="animals.php">Animals</a>
                    <a href="animal_sightings.php">Animal Sightings</a>
                    <a href="destinations.php">Destinations</a>
                    <a href="reviews.php" class="active">Reviews</a>
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
        <h1>Reviews</h1>

        <!-- Filter input and buttons -->
        <div class="filter-form">
            <input type="text" id="destinationFilter" placeholder="Filter by destination..." autocomplete="off" />
            <div class="autocomplete-suggestions" id="filterSuggestions"></div>
            <button id="filterButton">Filter</button>
            <button id="clearFilter" style="display:none;">Clear</button>
        </div>


        <div id="reviewsList">
            <!-- Feature cards# dynamically inputted here -->
        </div>

        <!-- Form to add new review -->
        <div class="add-review-form">
            <h2>Add a Review</h2>
            <form id="reviewForm" autocomplete="off">
                <div class="form-group" style="z-index:2;">

                    <label for="destinationInput">Destination:</label>
                    <input type="text" id="destinationInput" name="destinationInput"
                        placeholder="Enter or select a destination..." required>
                    <div class="autocomplete-suggestions" id="destinationSuggestions"></div>
                </div>
                <div class="form-group">

                    <label>Rating:</label>
                    <div class="star-rating" id="starRating">
                        <span data-value="5">&#9733;</span>
                        <span data-value="4">&#9733;</span>
                        <span data-value="3">&#9733;</span>
                        <span data-value="2">&#9733;</span>
                        <span data-value="1">&#9733;</span>
                    </div>
                    <input type="hidden" id="rating" name="rating" required>
                </div>
                <div class="form-group">
                    <label for="comment">Your Review:</label>
                    <textarea id="comment" name="comment" rows="4" required
                        placeholder="Write your review here..."></textarea>
                </div>

                <button type="submit">Submit Review</button>
            </form>

            <div id="reviewMsg"></div>
        </div>
    </div>

    <footer>
        <p>© 2024 Chloe's Travel Project | Made with ❤️</p>
    </footer>

    <script>
        // show reviews on page
        function displayReviews(reviews) {
            const reviewsList = document.getElementById('reviewsList');
            reviewsList.innerHTML = '';
            if (!reviews.length) {
                reviewsList.innerHTML = '<p>No reviews found.</p>';
                return;
            }
            reviews.forEach(review => {

                // Create stars based on rating number
                const stars = '&#9733;'.repeat(review.rating);
                const card = document.createElement('div');
                card.className = 'feature-card';
                card.innerHTML = `
                    <div class="icon">&#128205;</div>
                    <div class="content">
                        <h3>${review.destination_name}</h3>
                        <div class="rating">${stars}</div>
                        <p>${review.comment}</p>
                        <small>Reviewed on ${new Date(review.created_at).toLocaleDateString()}</small>
                    </div>
                `;
                reviewsList.appendChild(card);
            });
        }
    </script>

    <script src="reviews.js"></script>
</body>

</html>